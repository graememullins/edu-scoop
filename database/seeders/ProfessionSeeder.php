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
            ['name' => 'Mathematics'],
            ['name' => 'English'],
            ['name' => 'Science'],
            ['name' => 'Biology'],
            ['name' => 'Chemistry'],
            ['name' => 'Physics'],
            ['name' => 'History'],
            ['name' => 'Geography'],
            ['name' => 'Religious Education'],
            ['name' => 'Physical Education'],
            ['name' => 'Music'],
            ['name' => 'Art and Design'],
            ['name' => 'Design and Technology'],
            ['name' => 'Computer Science'],
            ['name' => 'Modern Foreign Languages'],
            ['name' => 'Drama'],
            ['name' => 'Business Studies'],
            ['name' => 'Economics'],
            ['name' => 'Psychology'],
            ['name' => 'Sociology'],
            ['name' => 'Primary'],
            ['name' => 'Early Years'],
            ['name' => 'Special Educational Needs (SEN)'],
            ['name' => 'Teaching Assistant'],
            ['name' => 'Cover Supervisor'],
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