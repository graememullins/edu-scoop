<?php

namespace App\Spiders;

use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NHSEnglandUrlSpider extends BasicSpider
{
    protected function initialRequests(): array
    {
        $requests = [];

        // Define custom headers
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language' => 'en-GB,en;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
            'Cache-Control' => 'max-age=0',
            'Sec-Ch-Ua-Platform' => 'Windows',
        ];

        // Fetch keywords and sources dynamically
        $keywords = DB::table('keywords')->where('status', 1)->get();
        $sources = DB::table('sources')->where('name', 'NHS England')->get();
        
        foreach ($sources as $source) {
            foreach ($keywords as $keyword) {
                for ($page = 1; $page <= 20; $page++) {
                    $url = "{$source->base_url}?keyword=" . urlencode($keyword->keyword) . "&skipPhraseSuggester=true&searchFormType=sortBy&language=en&page={$page}";

                    Log::info('Generated URL: ' . $url);

                    $requests[] = new Request(
                        'GET',
                        $url,
                        [$this, 'parse'],
                        [
                            'headers' => $headers, // Add headers here
                            'context' => [
                                'source_id' => $source->id,
                                'keyword_id' => $keyword->id,
                                'page' => $page,
                            ],
                        ]
                    );
                }
            }
        }

        return $requests; // Always return the requests array
    }

    public function parse(Response $response): \Generator
    {
        $body = $response->getBody();
        $crawler = new Crawler($body);
    
        // Correct selector to find each job result
        $jobListItems = $crawler->filter('li.search-result');
    
        // Log the number of job items found
        Log::info('Job Items Count: ' . $jobListItems->count());
    
        // Get context values from the Response object
        $requestOptions = $response->getRequest()->getOptions();
        $keywordId = $requestOptions['context']['keyword_id'] ?? null;
        $sourceId = $requestOptions['context']['source_id'] ?? null;
    
        // Check if context values exist
        if (!$keywordId || !$sourceId) {
            Log::warning('Missing context for keyword_id or source_id.');
            yield from [];
            return;
        }
    
        // Prepare an array to collect job data
        $jobs = [];
        $jobListItems->each(function (Crawler $node) use (&$jobs, $keywordId, $sourceId) {
            try {
                // Extract the job link and title
                $jobLink = $node->filter('a[data-test="search-result-job-title"]')->attr('href');
                $jobTitle = $node->filter('a[data-test="search-result-job-title"]')->text();

                $professionId = DB::table('keywords')->where('id', $keywordId)->value('profession_id');
    
                // Use regex to extract the job ID from the URL
                if ($jobLink && preg_match('/\/jobadvert\/([A-Za-z0-9-]+)/', $jobLink, $matches)) {
                    $jobId = $matches[1];
    
                    // Collect the job data
                    $jobs[] = [
                        'job_id' => $jobId,
                        'job_link' => $jobLink,
                        'job_title' => trim($jobTitle),
                        'keyword_id' => $keywordId,
                        'profession_id' => $professionId,
                        'source_id' => $sourceId,
                    ];
    
                    Log::info('Job Found', [
                        'job_id' => $jobId,
                        'job_link' => $jobLink,
                        'keyword_id' => $keywordId,
                        'profession_id' => $professionId,
                        'source_id' => $sourceId,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error parsing job item: ' . $e->getMessage());
            }
        });
    
        // Save jobs to the database
        foreach ($jobs as $job) {
            DB::table('nhs_england_jobs')->updateOrInsert(
                ['job_id' => $job['job_id']], // Ensure uniqueness of job_id
                [
                    'job_link' => $job['job_link'],
                    'source_id' => $job['source_id'],
                    'keyword_id' => $job['keyword_id'],
                    'profession_id' => $job['profession_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    
        // Update the last_run field in the keywords table
        DB::table('keywords')->where('id', $keywordId)->update([
            'last_run' => now(),
        ]);
    
        // Ensure the method satisfies the Generator return type
        yield from [];
    }      

    private function extractJobId($jobLink): string
    {
        // Extract job ID from the link, e.g., "/jobadvert/12345" -> "12345"
        if (preg_match('/\/jobadvert\/([A-Za-z0-9-]+)/', $jobLink, $matches)) {
            return $matches[1];
        }

        // If extraction fails, generate a hash of the link as fallback
        return Str::uuid()->toString();
    }
}