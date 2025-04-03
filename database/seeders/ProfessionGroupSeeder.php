<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            'STEM',
            'Humanities',
            'Languages',
            'Creative Arts',
            'Social Sciences',
            'Physical Education',
            'Primary & Early Education',
            'Other Support',
        ];

        foreach ($groups as $group) {
            DB::table('profession_groups')->insert([
                'name' => $group,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}