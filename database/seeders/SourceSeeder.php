<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            DB::table('sources')->insert([
                'name' => $source['name'],
                'base_url' => $source['base_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}