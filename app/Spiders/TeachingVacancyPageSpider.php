<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Http\Request;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\TeachingJob;

class TeachingVacancyPageSpider extends BasicSpider
{
    public array $startUrls;

    public static function initializeStartUrls(): array
    {
        return TeachingJob::where('is_scraped', false)
            ->pluck('job_link') // Full URLs now
            ->filter(fn ($url) => !empty($url))
            ->toArray();
    }

    public function __construct()
    {
        $this->startUrls = self::initializeStartUrls();
        parent::__construct();
    }

    public function startRequests(): Generator
    {
        foreach ($this->startUrls as $url) {
            Log::info('Dispatching request', ['uri' => $url]);
            yield Request::get($url, [$this, 'parse'])->withMeta(['job_link' => $url]);
        }
    }

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 3;
    public int $requestDelay = 1;

    public function parse(Response $response): Generator
    {
        $crawler = new Crawler($response->getBody());

        // Use meta value or fallback to URI
        $jobLink = $response->getRequest()->getMeta('job_link') ?? $response->getRequest()->getUri();
        $jobLink = trim(strtolower($jobLink)); // Normalize

        $startDate = null;
        $closingDate = null;
        $postedDate = null;

        try {
            $crawler->filter('.timeline-component__item')->each(function (Crawler $node) use (&$startDate, &$closingDate, &$postedDate) {
                $label = trim($node->filter('h3')->text());
                $value = $node->filter('p')->count() ? trim($node->filter('p')->text()) : null;

                switch ($label) {
                    case 'Closing date':
                        $closingDate = $value;
                        break;
                    case 'Date listed':
                        $postedDate = $value;
                        break;
                }
            });

            $formattedClosingDate = $this->convertDate($closingDate);
            $formattedPostedDate = $this->convertDate($postedDate);

            Log::debug("Parsed jobLink: $jobLink");
            Log::debug("Closing date: $closingDate, Posted date: $postedDate");

            $job = TeachingJob::whereRaw('LOWER(TRIM(job_link)) = ?', [$jobLink])->first();

            if (!$job) {
                Log::warning("No teaching job found with job_link = $jobLink");
            } else {
                $job->update([
                    'closing_date' => $formattedClosingDate,
                    'posted_date' => $formattedPostedDate,
                    'is_scraped' => true,
                    'updated_at' => now(),
                ]);
                Log::info("Updated job: {$job->job_id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to parse job page $jobLink: " . $e->getMessage());
        }

        yield from [];
    }

    private function convertDate($text): ?string
    {
        if (!$text) return null;

        // Match e.g. "1st September 2025"
        if (preg_match('/\d{1,2}(st|nd|rd|th)? \w+ \d{4}/', $text, $match)) {
            try {
                return Carbon::parse($match[0])->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Match e.g. "September 2025"
        if (preg_match('/\w+ \d{4}/', $text, $match)) {
            try {
                return Carbon::parse('1 ' . $match[0])->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}