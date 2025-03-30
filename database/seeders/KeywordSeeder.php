<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Profession;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KeywordSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        // Define keywords for each profession
        $keywordsByProfession = [
            'Mathematics' => [
                ['keyword' => 'Maths Teacher', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Mathematics Teacher', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Maths', 'last_run' => $now, 'status' => '1'],
            ],
            'Science' => [
                ['keyword' => 'Science Teacher', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Physics Teacher', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Chemistry Teacher', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Biology Teacher', 'last_run' => $now, 'status' => '1'],
            ],
        ];

        foreach ($keywordsByProfession as $professionName => $keywords) {
            $profession = Profession::where('name', $professionName)->first();

            if ($profession) {
                foreach ($keywords as $keywordData) {
                    DB::table('keywords')->insert([
                        'profession_id' => $profession->id,
                        'keyword' => $keywordData['keyword'],
                        'last_run' => $keywordData['last_run'],
                        'status' => $keywordData['status'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
