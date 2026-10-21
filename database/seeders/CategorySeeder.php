<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
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
