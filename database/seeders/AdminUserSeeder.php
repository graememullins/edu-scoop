<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(
            ['id' => '1',
            'first_name' => 'Graeme',
            'last_name' => 'Mullins',
            'email' => 'mail@graememullins.com',
            'client_id' => '1',
            'password' => bcrypt ('TugNuts2025!'),
            ]);

        User::create(
            ['id' => '2',
            'first_name' => 'John',
            'last_name' => 'Gannon',
            'email' => 'john@orionrsg.com',
            'client_id' => '2',
            'password' => bcrypt ('TugNuts2025!'),
            ]);
    }
}