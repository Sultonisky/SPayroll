<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;



class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        // Validate required admin credentials from .env
        $adminEmail = env('ADMIN_EMAIL');
        $adminName = env('ADMIN_NAME');
        $adminPassword = env('ADMIN_PASSWORD');

        if (!$adminEmail || !$adminName || !$adminPassword) {
            $this->command->error('Please set ADMIN_EMAIL, ADMIN_NAME, and ADMIN_PASSWORD in your .env file before seeding!');
            return;
        }

        // Create or update admin user
        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name'              => $adminName,
                'password'          => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'role'              => 'admin',
            ]
        );

        // 2. Seed Test Users (using UserFactory)
        User::factory()->count(10)->create([
            'role' => 'staff', // Use one of the allowed ENUM values
        ]);
    }
}
