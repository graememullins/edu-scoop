<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'name' => 'Squad Apps',
            'domain' => 'graememullins.com',
            'is_active' => true,
            'primary_contact_name' => 'Graeme Mullins',
            'primary_contact_email' => 'mail@graememullins.com',
            'notes' => 'Internal client for product development and testing.',
        ]);

        Client::create([
            'name' => 'Orion RSG',
            'domain' => 'orionrsg.com',
            'is_active' => true,
            'primary_contact_name' => 'John Gannon',
            'primary_contact_email' => 'john@orionrsg.com',
            'notes' => 'Orion RSG client for product development and testing.',
        ]);
    }
}