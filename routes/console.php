<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\TeachingJob;
use App\Models\Keyword;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use JustSteveKing\LaravelPostcodes\Facades\Postcode;

Artisan::command('spider:teaching-vacancy-urls', function () {
    $this->info('Running TeachingUrlSpider...');
    
    // Use Symfony Process to run the `roach:run` command
    $process = new Symfony\Component\Process\Process([
        'php', 'artisan', 'roach:run', 'App\\Spiders\\TeachingVacancyUrlSpider'
    ]);
    $process->setTimeout(3600); // Set a timeout if needed
    $process->run();

    if ($process->isSuccessful()) {
        $this->info('Spider completed successfully.');
    } else {
        $this->error('Error running the spider:');
        $this->error($process->getErrorOutput());
    }
})->describe('Run the NHSEnglandUrlSpider')->cron('0 8-20 * * *');

Artisan::command('spider:teaching-vacancy-pages', function () {
    $this->info('📘 Starting TeachingPageSpider with retry logic...');

    $maxAttempts = 3;
    $attempt = 1;

    do {
        $this->info("Attempt #$attempt: Running the spider...");

        $process = new Symfony\Component\Process\Process([
            'php', 'artisan', 'roach:run', 'App\\Spiders\\TeachingVacancyPageSpider'
        ]);
        $process->setTimeout(3600);
        $process->run();

        if ($process->isSuccessful()) {
            $this->info('Spider completed successfully.');
        } else {
            $this->error('Error running the spider:');
            $this->error($process->getErrorOutput());
            break;
        }

        // Reset unscraped jobs
        $resetCount = TeachingJob::whereNull('posted_date')
            ->update(['is_scraped' => false]);

        $this->info("Reset $resetCount teaching jobs where posted_date was null.");

        $remaining = TeachingJob::whereNull('posted_date')->count();
        $this->info("Remaining with posted_date NULL: $remaining");

        $attempt++;
    } while ($remaining > 0 && $attempt <= $maxAttempts);

    if ($remaining === 0) {
        $this->info('All jobs successfully scraped!');
    } else {
        $this->warn("Some jobs still missing posted_date after $maxAttempts attempts.");
    }

})->describe('Run the TeachingPageSpider and retry if any posted_date fields are null')->cron('15 8-20 * * *');


Artisan::command('jobs:validate-keywords', function () {
    $this->info('Validating unprocessed jobs for keyword assignment...');

    // Fetch only jobs where `is_scraped = 1` and `keyword_checked = 0`
    $jobs = TeachingJob::where('is_scraped', 1)
        ->where('keyword_checked', 0)
        ->get();

    $fixed = 0;
    $unchanged = 0;

    foreach ($jobs as $job) {
        $oldKeyword = Keyword::find($job->keyword_id);
        $oldKeywordName = $oldKeyword ? $oldKeyword->keyword : 'None';

        // Detect database type
        $dbDriver = DB::connection()->getDriverName();
        $likeOperator = $dbDriver === 'pgsql' ? 'ILIKE' : 'LIKE';

        // Correct way to search for a keyword inside job title (Works in MySQL & PostgreSQL)
        $correctKeyword = Keyword::where('status', 1)
            ->whereRaw("? $likeOperator CONCAT('%', keyword, '%')", [$job->job_title])
            ->first();

        if ($correctKeyword && $job->keyword_id !== $correctKeyword->id) {
            $newKeywordName = $correctKeyword->keyword;
            $job->update([
                'keyword_id' => $correctKeyword->id,
                'profession_id' => $correctKeyword->profession_id,
                'keyword_checked' => 1, // Mark job as checked
            ]);

            Log::info("Corrected job ID {$job->id}: Keyword changed from '{$oldKeywordName}' to '{$newKeywordName}'");
            $fixed++;
        } else {
            // If no change, still mark as checked
            $job->update(['keyword_checked' => 1]);
            $unchanged++;
        }
    }

    $this->info("{$fixed} keywords corrected, {$unchanged} were already correct.");
})->describe('Cross-check and correct assigned keywords for jobs')->everyFifteenMinutes();

Artisan::command('jobs:validate-postcodes', function () {
    $this->info('Validating postcodes for jobs...');

    $jobs = TeachingJob::where('post_code_validated', false)
        ->whereNotNull('post_code')
        ->get();

    $updated = 0;
    $failed = 0;

    foreach ($jobs as $job) {
        try {
            $response = Postcode::getPostcode($job->post_code);

            if (!empty($response) && isset($response->postcode)) {
                $job->update([
                    'region' => $response->region ?? null,
                    'longitude' => $response->longitude ?? null,
                    'latitude' => $response->latitude ?? null,
                    'nuts' => $response->nuts ?? null,
                    'pfa' => $response->pfa ?? null,
                    'post_code_validated' => true,
                ]);

                Log::info("Updated job ID {$job->id} with postcode: {$response->postcode}");
                $updated++;
            } else {
                Log::warning("No data found for postcode '{$job->post_code}' (Job ID {$job->id})");
                $failed++;
            }
        } catch (\Throwable $e) {
            Log::error("Postcode lookup failed for job ID {$job->id} ({$job->post_code}): " . $e->getMessage());
            $failed++;
        }
    }

    $this->info("Postcode update complete: {$updated} updated, {$failed} failed.");
})->describe('Update jobs with region post code data')->everyThirtyMinutes();