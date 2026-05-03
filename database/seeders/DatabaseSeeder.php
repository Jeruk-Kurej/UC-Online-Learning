<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Locations (IndoRegion)
        $this->command->info('🚀 Seeding IndoRegion Data...');
        $this->call(IndoRegionSeeder::class);

        // 2. Categories
        $this->call(CategorySeeder::class);

        // 3. Users
        $this->call(UserSeeder::class);

        // 4. Businesses (depends on Users and Categories)
        $this->call(EnhancedBusinessSeeder::class);

        $this->command->info('✨ Database seeding completed successfully!');
    }
}
