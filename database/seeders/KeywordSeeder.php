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
                'Maths Teacher',
            ],
            'English' => [
                'English Teacher',
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