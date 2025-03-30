<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ProfessionSeeder;
use Database\Seeders\KeywordSeeder;
use Database\Seeders\SourceSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\TeachingJobSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(SourceSeeder::class);
        $this->call(ProfessionSeeder::class);
        $this->call(KeywordSeeder::class);
        //$this->call(TeachingJobSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(RoleSeeder::class);
    }
}