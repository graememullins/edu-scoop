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
            ['name' => 'NHS England', 'base_url' => 'https://www.jobs.nhs.uk/candidate/search/results'],
            ['name' => 'NHS Scotland', 'base_url' => 'https://apply.jobs.scot.nhs.uk/Home/Job'],
            ['name' => 'NHS Wales', 'base_url' => 'https://www.jobs.nhs.uk/candidate/search/results?searchFormType=main&location=Wales'],
            ['name' => 'NHS Northern Ireland', 'base_url' => 'https://jobs.hscni.net/Search'],
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