<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the super_admin role (or find it if it already exists)
        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        // Assign the role to user with ID = 1
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $role->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => 1,
        ]);
    }
}