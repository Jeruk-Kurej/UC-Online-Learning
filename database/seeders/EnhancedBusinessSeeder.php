<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EnhancedBusinessSeeder extends Seeder
{
    // --- Sample ventures: owner ("admin" or an email), category, details, two offerings and one co-owner ---

    private const VENTURES = [
        [
            'owner' => 'admin',
            'category' => 'any',
            'business' => [
                'name' => 'Sample Venture',
                'description' => 'A wonderful sample business venture for all entrepreneurs.',
                'is_featured' => true,
                'type' => 'entrepreneur',
                'offering_type' => 'product',
                'phone_number' => '08123456789',
                'address' => 'Jl. Jendral Sudirman No. 12',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
            ],
            'offerings' => [
                ['Signature T-Shirt', 'High quality premium cotton t-shirt.', 149000],
                ['Premium Hoodie', 'Comfortable and warm premium hoodie.', 299000],
            ],
            'member' => ['student@uco.com', 'Co-Owner'],
        ],
        [
            'owner' => 'student@uco.com',
            'category' => 'any',
            'business' => [
                'name' => 'EcoFresh Catering',
                'description' => 'Providing healthy and eco-friendly catering services for schools and events.',
                'is_featured' => false,
                'type' => 'entrepreneur',
                'offering_type' => 'service',
                'phone_number' => '08234567890',
                'address' => 'Jl. Kebon Jeruk No. 5',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
            ],
            'offerings' => [
                ['Daily Student Meal Box', 'Balanced nutrition daily meal box for active students.', 35000],
                ['Event Buffet Service', 'Premium healthy buffet setup for events.', 125000],
            ],
            'member' => ['budi@uco.com', 'Partner & Head of Operations'],
        ],
        [
            'owner' => 'budi@uco.com',
            'category' => 'tech',
            'business' => [
                'name' => 'Budi Solutions',
                'description' => 'Modern digital marketing and SEO consultancy services.',
                'is_featured' => false,
                'type' => 'intrapreneur',
                'offering_type' => 'service',
                'phone_number' => '08111222333',
                'address' => 'Jl. Gajah Mada No. 44',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
            ],
            'offerings' => [
                ['SEO Audit & Optimization', 'Complete technical SEO audit and site performance enhancement.', 2500000],
                ['Monthly Digital Marketing', 'Social media management and targeted advertising.', 5000000],
            ],
            'member' => ['siti@uco.com', 'Lead Marketing Specialist'],
        ],
        [
            'owner' => 'siti@uco.com',
            'category' => 'creative',
            'business' => [
                'name' => 'Siti Creatives',
                'description' => 'Premium graphic design, brand identity, and content marketing.',
                'is_featured' => true,
                'type' => 'entrepreneur',
                'offering_type' => 'product',
                'phone_number' => '08556677889',
                'address' => 'Jl. Merdeka No. 10',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
            ],
            'offerings' => [
                ['Brand Identity Package', 'Complete visual guidelines, logos, and typography guidelines.', 4500000],
                ['Custom UI/UX Website Design', 'Bespoke modern UI/UX design prototype for web and mobile.', 6000000],
            ],
            'member' => ['agus@uco.com', 'Creative Director'],
        ],
    ];

    public function run(): void
    {
        $category = Category::first();
        $categories = [
            'any' => $category,
            'tech' => Category::where('name', 'like', '%Tech%')->orWhere('name', 'like', '%digital%')->first() ?? $category,
            'creative' => Category::where('name', 'like', '%Creative%')->orWhere('name', 'like', '%jasa%')->first() ?? $category,
        ];

        foreach (self::VENTURES as $venture) {
            $owner = $this->user($venture['owner']);
            $ventureCategory = $categories[$venture['category']];
            if (! $owner || ! $ventureCategory) {
                continue;
            }

            $business = Business::firstOrCreate(['name' => $venture['business']['name']], [
                'user_id' => $owner->id,
                'category_id' => $ventureCategory->id,
                'slug' => Str::slug($venture['business']['name']),
                'is_visible' => true,
                'operational_status' => 'active',
            ] + $venture['business']);

            foreach ($venture['offerings'] as $index => [$name, $description, $price]) {
                $business->products()->firstOrCreate(['name' => $name], [
                    'description' => $description,
                    'price' => $price,
                    'sort_order' => $index + 1,
                ]);
            }

            [$memberEmail, $position] = $venture['member'];
            if ($member = $this->user($memberEmail)) {
                $business->members()->syncWithoutDetaching([$member->id => ['position' => $position]]);
            }
        }
    }

    private function user(string $who): ?User
    {
        return $who === 'admin' ? User::where('role', 'admin')->first() : User::where('email', $who)->first();
    }
}
