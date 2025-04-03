<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Profession;
use Carbon\Carbon;

class KeywordSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        $keywordsByProfession = [
            'Mathematics' => [
                'Mathematics Teacher', 'Maths Teacher', 'Maths',
            ],
            'English' => [
                'English Teacher', 'English Literature Teacher', 'English Language Teacher',
            ],
            'Science' => [
                'Science Teacher', 'Chemistry Teacher', 'Biology Teacher', 'Physics Teacher',
            ],
            'Biology' => [
                'Biology Teacher', 'Teacher of Biology',
            ],
            'Chemistry' => [
                'Chemistry Teacher', 'Teacher of Chemistry',
            ],
            'Physics' => [
                'Physics Teacher', 'Teacher of Physics',
            ],
            'History' => [
                'History Teacher', 'Teacher of History',
            ],
            'Geography' => [
                'Geography Teacher', 'Teacher of Geography',
            ],
            'Religious Education' => [
                'RE Teacher', 'Religious Education Teacher',
            ],
            'Physical Education' => [
                'PE Teacher', 'Physical Education Teacher', 'Girls PE Teacher',
            ],
            'Music' => [
                'Music Teacher', 'Teacher of Music',
            ],
            'Art and Design' => [
                'Art Teacher', 'Art and Design Teacher',
            ],
            'Design and Technology' => [
                'Design and Technology Teacher', 'DT Teacher',
            ],
            'Computer Science' => [
                'Computer Science Teacher', 'ICT Teacher', 'Computing Teacher',
            ],
            'Modern Foreign Languages' => [
                'MFL Teacher', 'French Teacher', 'Spanish Teacher', 'German Teacher',
            ],
            'Drama' => [
                'Drama Teacher', 'Performing Arts Teacher',
            ],
            'Business Studies' => [
                'Business Teacher', 'Business Studies Teacher',
            ],
            'Economics' => [
                'Economics Teacher', 'Teacher of Economics',
            ],
            'Psychology' => [
                'Psychology Teacher', 'Teacher of Psychology',
            ],
            'Sociology' => [
                'Sociology Teacher', 'Teacher of Sociology',
            ],
            'Primary' => [
                'Primary Teacher', 'KS1 Teacher', 'KS2 Teacher',
            ],
            'Early Years' => [
                'EYFS Teacher', 'Reception Teacher', 'Nursery Teacher',
            ],
            'Special Educational Needs (SEN)' => [
                'SEN Teacher', 'SEND Teacher', 'SENCO',
            ],
            'Teaching Assistant' => [
                'Teaching Assistant', 'Learning Support Assistant', 'HLTA',
            ],
            'Cover Supervisor' => [
                'Cover Supervisor', 'Lesson Cover', 'Unqualified Teacher',
            ],
        ];

        foreach ($keywordsByProfession as $professionName => $keywords) {
            $profession = Profession::where('name', $professionName)->first();

            if ($profession) {
                foreach ($keywords as $keyword) {
                    DB::table('keywords')->insert([
                        'profession_id' => $profession->id,
                        'keyword' => $keyword,
                        'last_run' => $now,
                        'status' => '1',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                $this->command->warn("Profession not found: {$professionName}");
            }
        }
    }
}