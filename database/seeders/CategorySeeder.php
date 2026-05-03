<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Kuliner',
            'Teknologi',
            'Fashion',
            'Jasa',
            'Pendidikan',
            'Kesehatan',
            'Kesenian & Kerajinan',
            'Konstruksi',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
            ], [
                'slug' => Str::slug($category),
            ]);
        }
    }
}
