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
        User::firstOrCreate([
            'email' => 'admin@uco.com',
        ], [
            'name' => 'Admin UCO',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        // Student User
        User::firstOrCreate([
            'email' => 'student@uco.com',
        ], [
            'name' => 'Student UCO',
            'password' => Hash::make('password'),
            'role' => 'user',
            'student_status' => 'active',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        // Alumni User
        User::firstOrCreate([
            'email' => 'alumni@uco.com',
        ], [
            'name' => 'Alumni UCO',
            'password' => Hash::make('password'),
            'role' => 'user',
            'student_status' => 'alumni',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        // Additional varied users
        User::firstOrCreate([
            'email' => 'budi@uco.com',
        ], [
            'name' => 'Budi Santoso',
            'password' => Hash::make('password'),
            'role' => 'user',
            'student_status' => 'active',
            'major' => 'Business Management',
            'current_status' => 'Entrepreneur',
            'year_of_enrollment' => '2023',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'siti@uco.com',
        ], [
            'name' => 'Siti Aminah',
            'password' => Hash::make('password'),
            'role' => 'user',
            'student_status' => 'active',
            'major' => 'Information Technology',
            'current_status' => 'Tech Founder',
            'year_of_enrollment' => '2022',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'agus@uco.com',
        ], [
            'name' => 'Agus Wijaya',
            'password' => Hash::make('password'),
            'role' => 'user',
            'student_status' => 'alumni',
            'major' => 'Creative Arts',
            'current_status' => 'Agency Owner',
            'year_of_enrollment' => '2019',
            'graduate_year' => '2023',
            'is_visible' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Users seeded successfully!');
    }
}
