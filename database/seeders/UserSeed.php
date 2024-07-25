<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('Admin@123'),
        ]);

        // Create a user
        \App\Models\User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'role' => 'user',
            'password' => bcrypt('User@123'),
        ]);
    }
}
