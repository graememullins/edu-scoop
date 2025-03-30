<?php

namespace App\Spiders;

use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TeachingVacancyUrlSpider extends BasicSpider
{
    protected function initialRequests(): array
    {
        $requests = [];

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

        $keywords = DB::table('keywords')->where('status', 1)->get();
        $sources = DB::table('sources')->where('name', 'Teaching Vacancies')->get();

        foreach ($sources as $source) {
            foreach ($keywords as $keyword) {
                for ($page = 1; $page <= 5; $page++) {
                    $queryParams = http_build_query([
                        'visa_sponsorship_availability' => [''],
                        'teaching_job_roles' => ['', 'teacher'],
                        'support_job_roles' => [''],
                        'phases' => [''],
                        'subjects' => [''],
                        'ect_statuses' => [''],
                        'organisation_types' => [''],
                        'school_types' => [''],
                        'working_patterns' => [''],
                        'quick_apply' => [''],
                        'previous_keyword' => $keyword->keyword,
                        'organisation_slug' => '',
                        'keyword' => $keyword->keyword,
                        'location' => '',
                        'radius' => '0',
                        'sort_by' => 'publish_on',
                        'page' => $page,
                    ]);

                    $url = "{$source->base_url}?{$queryParams}";
                    Log::info("Generated URL: $url");

                    $requests[] = new Request(
                        'GET',
                        $url,
                        [$this, 'parse'],
                        [
                            'headers' => $headers,
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

        return $requests;
    }

    public function parse(Response $response): \Generator
    {
        try {
            $crawler = new Crawler($response->getBody());
    
            $requestOptions = $response->getRequest()->getOptions();
            $keywordId = $requestOptions['context']['keyword_id'] ?? null;
            $sourceId = $requestOptions['context']['source_id'] ?? null;
    
            if (!$keywordId || !$sourceId) {
                Log::warning('Missing context for keyword_id or source_id.');
                yield from [];
                return;
            }
    
            $jobNodes = $crawler->filter('div.search-results__item a.view-vacancy-link');
            Log::info('Found job link nodes: ' . $jobNodes->count());
    
            $jobs = [];
            $jobNodes->each(function (Crawler $node) use (&$jobs, $keywordId, $sourceId) {
                try {
                    $relativeLink = $node->attr('href');
                    $fullLink = 'https://teaching-vacancies.service.gov.uk' . $relativeLink;
                    $jobTitle = $node->text();
    
                    if (preg_match('/\/jobs\/([a-z0-9-]+)/', $relativeLink, $matches)) {
                        $jobId = $matches[1];
                        $professionId = DB::table('keywords')->where('id', $keywordId)->value('profession_id');
    
                        $jobs[] = [
                            'job_id' => $jobId,
                            'job_link' => $fullLink,
                            'job_title' => trim($jobTitle),
                            'keyword_id' => $keywordId,
                            'profession_id' => $professionId,
                            'source_id' => $sourceId,
                        ];
    
                        Log::info('Job Found', compact('jobId', 'fullLink'));
                    }
                } catch (\Exception $e) {
                    Log::error('Error parsing a job node: ' . $e->getMessage());
                }
            });
    
            foreach ($jobs as $job) {
                DB::table('teaching_jobs')->updateOrInsert(
                    ['job_id' => $job['job_id']],
                    [
                        'job_link' => $job['job_link'],
                        'job_title' => $job['job_title'],
                        'source_id' => $job['source_id'],
                        'keyword_id' => $job['keyword_id'],
                        'profession_id' => $job['profession_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
    
            DB::table('keywords')->where('id', $keywordId)->update([
                'last_run' => now(),
            ]);
    
        } catch (\Throwable $e) {
            Log::error('Fatal error in parse(): ' . $e->getMessage());
        }
    
        yield from [];
    }    
}