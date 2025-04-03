<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    public function run()
    {
        $sources = [
            ['name' => 'Teaching Vacancies', 'base_url' => 'https://teaching-vacancies.service.gov.uk/jobs'],
        ];

        foreach ($sources as $source) {
            DB::table('sources')->updateOrInsert(
                ['name' => $source['name']],
                [
                    'base_url' => $source['base_url'],
                    'updated_at' => now(),
                    'created_at' => now(), // optional: only used if new
                ]
            );
        }
    }
}