<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;

class EnhancedBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $category = Category::first();

        if ($user && $category) {
            Business::firstOrCreate([
                'name' => 'Sample Venture',
            ], [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'slug' => Str::slug('Sample Venture'),
                'description' => 'A wonderful sample business venture.',
                'is_visible' => true,
                'is_featured' => true,
                'type' => 'entrepreneur',
                'operational_status' => 'active',
                'offering_type' => 'product',
            ]);
        }
    }
}
