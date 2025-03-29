<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    public function run()
    {
        $professions = [
            ['name' => 'Pharmacy'],
            ['name' => 'Radiography'],
            ['name' => 'Biomedical Science'],
            ['name' => 'Sterile Services'],
            ['name' => 'Audiology'],
            ['name' => 'Occupational Therapy'],
            ['name' => 'Physiotherapy'],
            ['name' => 'Mental Health'],
            ['name' => 'Operating Theatre'],
            ['name' => 'Speech and Language'],
        ];

        foreach ($professions as $profession) {
            DB::table('professions')->insert([
                'name' => $profession['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}