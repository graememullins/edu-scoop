<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\TeachingJob;
use App\Models\Keyword;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use JustSteveKing\LaravelPostcodes\Facades\Postcode;

Artisan::command('spider:teaching-vacancy-spider', function () {
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
    $this->info('Running TeachingPageSpider...');
    
    // Use Symfony Process to run the `roach:run` command
    $process = new Symfony\Component\Process\Process([
        'php', 'artisan', 'roach:run', 'App\\Spiders\\TeachingVacancyPageSpider'
    ]);
    $process->setTimeout(3600); // Set a timeout if needed
    $process->run();

    if ($process->isSuccessful()) {
        $this->info('Spider completed successfully.');
    } else {
        $this->error('Error running the spider:');
        $this->error($process->getErrorOutput());
    }
})->describe('Run the TeachingPageSpider')->cron('15 8-20 * * *');

Artisan::command('teaching-jobs:reset-unscraped', function () {
    $updated = TeachingJob::whereNull('posted_date')
        ->update(['is_scraped' => false]);

    $this->info("Reset $updated teaching jobs where posted_date was null.");
});

Artisan::command('jobs:validate-keywords', function () {
    $this->info('Validating unprocessed jobs for keyword assignment...');

    // Fetch only jobs where `is_scraped = 1` and `keyword_checked = 0`
    $jobs = NhsEnglandJob::where('is_scraped', 1)
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
})->describe('Cross-check and correct assigned keywords for NHS England jobs')->everyFifteenMinutes();

Artisan::command('jobs:validate-postcodes', function () {
    $this->info('Validating postcodes for NHS England jobs...');

    $jobs = NhsEnglandJob::where('post_code_validated', false)
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
                    'ccg' => $response->ccg ?? null,
                    'post_code_validated' => true,
                ]);

                Log::info("✅ Updated job ID {$job->id} with postcode: {$response->postcode}");
                $updated++;
            } else {
                Log::warning("⚠️ No data found for postcode '{$job->post_code}' (Job ID {$job->id})");
                $failed++;
            }
        } catch (\Throwable $e) {
            Log::error("Postcode lookup failed for job ID {$job->id} ({$job->post_code}): " . $e->getMessage());
            $failed++;
        }
    }

    $this->info("Postcode update complete: {$updated} updated, {$failed} failed.");
})->describe('Update NHS England jobs with region, coordinates, and CCG from postcode data')->everyThirtyMinutes();

Artisan::command('jobs:backfill-profession', function () {
    $this->info('Backfilling profession_id on NHS jobs...');

    $count = 0;

    \App\Models\NhsEnglandJob::with('keyword') // eager-load keyword
        ->whereNotNull('keyword_id')
        ->chunk(500, function ($jobs) use (&$count) {
            foreach ($jobs as $job) {
                if ($job->keyword && $job->profession_id !== $job->keyword->profession_id) {
                    $job->profession_id = $job->keyword->profession_id;
                    $job->save();

                    $count++;
                }
            }
        });

    $this->info("Updated {$count} jobs with correct profession_id.");
})->describe('One-off command to populate profession_id based on keyword_id');