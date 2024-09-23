<?php
// database/seeders/RolesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Define roles with specific IDs
        $roles = [
            [
                'id' => 1,
                'role_name' => 'admin',
            ],
            [
                'id' => 2,
                'role_name' => 'researcher',
            ],
            [
                'id' => 3,
                'role_name' => 'reviewer',
            ],
            [
                'id' => 4,
                'role_name' => 'coe',
            ],
        ];

        // Insert roles into the roles table
        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}
