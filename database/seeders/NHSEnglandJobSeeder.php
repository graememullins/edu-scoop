<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NHSEnglandJob;

class NHSEnglandJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NHSEnglandJob::factory()->count(50)->create(); // Creates 50 fake jobs
    }
}
