<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;
use App\Models\NhsEnglandJob;
use Illuminate\Support\Facades\Log;
use Exception;

class NHSEnglandPageSpider extends BasicSpider
{
    // Initialize startUrls with dynamic URLs from the database
    public array $startUrls;

    // Static method to initialize startUrls before instantiation
    public static function initializeStartUrls(): array
    {
        // Fetch only job_id's from the database
        $jobs = NhsEnglandJob::where('is_scraped', '=', 0)->pluck('job_id')->toArray();
        $urls = [];
    
        // Log the fetched job IDs
        Log::info('Fetched Job IDs: ' . json_encode($jobs));
    
        foreach ($jobs as $jobId) {
            // Check and log each job_id
            if (!empty($jobId) && $jobId !== '0') {
                $url = 'https://www.jobs.nhs.uk/candidate/jobadvert/' . $jobId;
                $urls[] = $url;
                Log::info('Generated URL: ' . $url);
            } else {
                Log::warning('Invalid or empty job ID found: ' . $jobId);
            }
        }
    
        // Log the generated URLs
        Log::info('Generated URLs: ' . json_encode($urls));
    
        // Check if URLs array is empty
        if (empty($urls)) {
            Log::error('No valid job URLs generated.');
        }

        return $urls;
    }

    // Override the constructor to initialize startUrls before parent
    public function __construct()
    {
        $this->startUrls = self::initializeStartUrls();
        parent::__construct();
    }

    public function startRequests(): \Generator
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language' => 'en,en-GB;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
            'Cache-Control' => 'max-age=0',
            'Sec-Ch-Ua-Platform' => 'Windows',
        ];
    
        foreach ($this->startUrls as $url) {
            Log::info('Requesting URL: ' . $url);  // Log each request URL
            yield Request::get($url, [$this, 'parse'])->setHeaders($headers);
        }
    }

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /**
     * @return Generator<ParseResult>
     */
    private function convertDateFormat($date)
    {
        try {
            return Carbon::createFromFormat('d F Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return 'Invalid date'; // Handle invalid date formats gracefully
        }
    }

    public function parse(\RoachPHP\Http\Response $response): \Generator
    {
        try {
            $urlParts = explode('/', $response->getRequest()->getUri());
            $jobId = end($urlParts);
    
            Log::info('Parsing job ID: ' . $jobId);
    
            // Ensure elements exist before accessing them
            $title = $response->filter('h1')->count() ? $response->filter('h1')->text() : 'Title not found';
            Log::info('Job Title: ' . $title);
    
            $closingDateElement = $response->filter('#closing_date')->count() ? $response->filter('#closing_date')->text() : 'Closing date not found';
            preg_match('/(\d{1,2}\s\w+\s\d{4})/', $closingDateElement, $matches);
            $fullClosingDate = $matches[1] ?? 'No closing date found';
    
            $trust = $response->filter('#employer_name')->count() ? $response->filter('#employer_name')->text() : 'Trust not found';
    
            $datePostedElement = $response->filter('#date_posted_heading')->count() ? $response->filter('#date_posted_heading')->nextAll('p')->first()->text() : 'Date posted not found';
            preg_match('/(\d{1,2}\s\w+\s\d{4})/', $datePostedElement, $datePostedMatches);
            $fullDatePosted = $datePostedMatches[1] ?? 'No date posted found';
    
            $closingDate = $this->convertDateFormat(trim($fullClosingDate));
            $datePosted = $this->convertDateFormat(trim($fullDatePosted));
    
            $referenceNumber = null;

            // Check if '#job_reference_number' exists and get its text if available
            if ($response->filter('#job_reference_number')->count()) {
                $referenceNumber = $response->filter('#job_reference_number')->text();
            }
            // If not found, check if '#trac-job-reference' exists and get its text
            elseif ($response->filter('#trac-job-reference')->count()) {
                $referenceNumber = $response->filter('#trac-job-reference')->text();
            }
            // If neither is found, use job_id as the fallback
            if (is_null($referenceNumber)) {
                $referenceNumber = $jobId;
            }
    
            $band = $response->filter('#payscheme-band')->count() ? $response->filter('#payscheme-band')->text() : '0';
            preg_match('/\d+/', $band, $matches);
            $bandNumber = $matches[0] ?? '0'; // Default to '0' if no match is found
    
            $contactDetailsJobTitle = $response->filter('#contact_details_job_title')->count() ? $response->filter('#contact_details_job_title')->text() : null;
            $contactDetailsName = $response->filter('#contact_details_name')->count() ? $response->filter('#contact_details_name')->text() : 'No name available';
            $contactDetailsEmail = $response->filter('#contact_details_email')->count() ? $response->filter('#contact_details_email')->text() : 'No email available';
            $contactDetailsPhone = $response->filter('#contact_details_number')->count() ? $response->filter('#contact_details_number')->text() : 'No Number available';

            $contractType= $response->filter('#contract_type')->count() ? $response->filter('#contract_type')->text() : 'No type available';
    
            $employerAddressLine1 = $response->filter('#employer_address_line_1')->count() ? $response->filter('#employer_address_line_1')->text() : 'No address line 1 available';
            $employerAddressLine2 = $response->filter('#employer_address_line_2')->count() ? $response->filter('#employer_address_line_2')->text() : 'No address line 2 available';
            $employerTown = $response->filter('#employer_town')->count() ? $response->filter('#employer_town')->text() : 'No town available';
            $employerPostcode = $response->filter('#employer_postcode')->count() ? $response->filter('#employer_postcode')->text() : 'No postcode available';
    
            $employerWebsiteUrl = $response->filter('#employer_website_url a')->count() ? $response->filter('#employer_website_url a')->attr('href') : 'No website available';
    
            $jobData = [
                //'job_id' => $jobId,
                'job_title' => $title,
                'closing_date' => $closingDate,
                'trust' => $trust,
                'posted_date' => $datePosted,
                'reference_number' => $referenceNumber,
                'contract_type' => $contractType,
                'band' => $bandNumber,
                'contact_job_title' => $contactDetailsJobTitle,
                'contact_name' => $contactDetailsName,
                'contact_email' => $contactDetailsEmail,
                'contact_phone' => $contactDetailsPhone,
                'address_line_1' => $employerAddressLine1,
                'address_line_2' => $employerAddressLine2,
                'town' => $employerTown,
                'post_code' => $employerPostcode,
                //'website_url' => $employerWebsiteUrl,
                'is_scraped' => 1,
            ];

            //dd ($jobData);

            // update or create the job in the database
            NhsEnglandJob::updateOrCreate(
                ['job_id' => $jobId],
                $jobData
            );
    
            Log::info('Scraping complete for job ID: ' . $jobId);
    
            // Yield the job data as a parsed item
            yield $this->item($jobData);
    
        } catch (\Exception $e) {
            Log::error('Error parsing response for job ID ' . $jobId . ': ' . $e->getMessage());
        }
    }
}