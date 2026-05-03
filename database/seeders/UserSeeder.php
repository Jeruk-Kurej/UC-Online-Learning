<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table.
     */
    public function run(): void
    {
        $this->command->info('🔄 Seeding Users...');

        // Admin User
        User::create([
            'name' => 'Admin UCO',
            'email' => 'admin@uco.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        // Student User
        User::create([
            'name' => 'Student UCO',
            'email' => 'student@uco.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'student_status' => 'active',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        // Alumni User
        User::create([
            'name' => 'Alumni UCO',
            'email' => 'alumni@uco.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'student_status' => 'alumni',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Users seeded successfully!');
    }
}
