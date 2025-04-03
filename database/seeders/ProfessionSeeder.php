<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    public function run()
    {
        $professions = [
            ['name' => 'Mathematics', 'group_id' => 1],
            ['name' => 'English', 'group_id' => 3],
            ['name' => 'Science', 'group_id' => 1],
            ['name' => 'Biology', 'group_id' => 1],
            ['name' => 'Chemistry', 'group_id' => 1],
            ['name' => 'Physics', 'group_id' => 1],
            ['name' => 'History', 'group_id' => 2],
            ['name' => 'Geography', 'group_id' => 2],
            ['name' => 'Religious Education', 'group_id' => 2],
            ['name' => 'Physical Education', 'group_id' => 6],
            ['name' => 'Music', 'group_id' => 4],
            ['name' => 'Art and Design', 'group_id' => 4],
            ['name' => 'Design and Technology', 'group_id' => 1],
            ['name' => 'Computer Science', 'group_id' => 1],
            ['name' => 'Modern Foreign Languages', 'group_id' => 3],
            ['name' => 'Drama', 'group_id' => 4],
            ['name' => 'Business Studies', 'group_id' => 5],
            ['name' => 'Economics', 'group_id' => 5],
            ['name' => 'Psychology', 'group_id' => 5],
            ['name' => 'Sociology', 'group_id' => 5],
            ['name' => 'Primary', 'group_id' => 7],
            ['name' => 'Early Years', 'group_id' => 7],
            ['name' => 'Special Educational Needs (SEN)', 'group_id' => 7],
            ['name' => 'Teaching Assistant', 'group_id' => 8],
            ['name' => 'Cover Supervisor', 'group_id' => 8],
        ];

        foreach ($professions as $profession) {
            DB::table('professions')->insert([
                'name' => $profession['name'],
                'profession_group_id' => $profession['group_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
