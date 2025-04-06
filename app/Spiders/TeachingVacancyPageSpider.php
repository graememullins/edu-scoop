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
            ->pluck('job_link')
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

        $closingDate = null;
        $postedDate = null;

        try {
            // Timeline dates
            $crawler->filter('.timeline-component__item')->each(function (Crawler $node) use (&$closingDate, &$postedDate) {
                $label = trim($node->filter('h3')->text());
                $value = $node->filter('p')->count() ? trim($node->filter('p')->text()) : null;

                if ($label === 'Closing date') {
                    $closingDate = $value;
                } elseif ($label === 'Date listed') {
                    $postedDate = $value;
                }
            });

            $formattedClosingDate = $this->convertDate($closingDate);
            $formattedPostedDate = $this->convertDate($postedDate);

            // Extract job summary values
            $summaryData = [];
            $contactEmail = null;
            $contactPhone = null;
            $website = null;

            $crawler->filter('.govuk-summary-list__row')->each(function (Crawler $row) use (&$summaryData, &$contactEmail, &$contactPhone, &$website) {
                $label = trim($row->filter('dt')->text());
                $valueNode = $row->filter('dd');
                $value = $valueNode->count() ? trim($valueNode->text()) : null;

                // Check for website href if label is "School website"
                if ($label === 'School website') {
                    $linkNode = $valueNode->filter('a');
                    if ($linkNode->count()) {
                        $website = rtrim($linkNode->attr('href'), '/');
                    }
                }

                $summaryData[$label] = $value;

                if (!$contactEmail && $label === 'Email address' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $contactEmail = $value;
                }

                if (!$contactPhone && $label === 'Phone number' && !empty($value)) {
                    $contactPhone = $value;
                }
            });

            $subject = $summaryData['Subject'] ?? $summaryData['Subjects'] ?? null;
            $educationPhase = $summaryData['Education phase'] ?? null;
            $ageRange = $summaryData['Age range'] ?? null;
            $contractType = $summaryData['Contract type'] ?? null;

            // Clean school type
            $schoolTypeRaw = $summaryData['School type'] ?? null;
            $schoolType = $schoolTypeRaw ? preg_replace('/,\s*ages.+$/i', '', $schoolTypeRaw) : null;

            // Extract just the number from school size
            $schoolSizeRaw = $summaryData['School size'] ?? null;
            $schoolSize = null;
            if ($schoolSizeRaw && preg_match('/\d+/', $schoolSizeRaw, $matches)) {
                $schoolSize = (int) $matches[0];
            }

            // Fallback: scan entire page for email or phone if not already found
            if (!$contactEmail || !$contactPhone) {
                $bodyText = $crawler->text();

                if (!$contactEmail) {
                    if (preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.(?:ac\.uk|co\.uk|org\.uk|gov\.uk|sch\.uk|com|net|org|info|co|uk)\b/i', $bodyText, $emailMatches)) {
                        foreach ($emailMatches[0] as $email) {
                            if (
                                !str_contains($email, 'sentry.io') &&
                                !str_contains($email, 'noreply') &&
                                !str_contains($email, 'donotreply')
                            ) {
                                $contactEmail = $email;
                                break;
                            }
                        }
                    }
                }

                if (!$contactPhone) {
                    if (preg_match('/\(?0\d{2,4}\)?[\s.-]?\d{3,4}[\s.-]?\d{3,4}/', $bodyText, $phoneMatches)) {
                        $contactPhone = $phoneMatches[0];
                    }
                }
            }

            Log::debug("Parsed jobLink: $jobLink");
            Log::debug("Closing date: $closingDate, Posted date: $postedDate");

            $job = TeachingJob::whereRaw('LOWER(TRIM(job_link)) = ?', [$jobLink])->first();

            if (!$job) {
                Log::warning("No teaching job found with job_link = $jobLink");
            } else {
                $job->update([
                    'closing_date' => $formattedClosingDate,
                    'posted_date' => $formattedPostedDate,
                    'subject' => $subject,
                    'education_phase' => $educationPhase,
                    'age_range' => $ageRange,
                    'contract_type' => $contractType,
                    'school_type' => $schoolType,
                    'school_size' => $schoolSize,
                    'contact_email' => $contactEmail,
                    'contact_phone' => $contactPhone,
                    'website' => $website,
                    'is_scraped' => true,
                    'updated_at' => now(),
                ]);
                Log::info("✅ Updated job: {$job->job_id}");
            }
        } catch (\Exception $e) {
            Log::error("❌ Failed to parse job page $jobLink: " . $e->getMessage());
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
