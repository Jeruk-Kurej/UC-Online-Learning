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
        $admin = User::where('role', 'admin')->first();
        $studentUser = User::where('email', 'student@uco.com')->first();
        $budiUser = User::where('email', 'budi@uco.com')->first();
        $sitiUser = User::where('email', 'siti@uco.com')->first();
        $agusUser = User::where('email', 'agus@uco.com')->first();

        $category = Category::first();
        $techCategory = Category::where('name', 'like', '%Tech%')->orWhere('name', 'like', '%digital%')->first() ?? $category;
        $creativeCategory = Category::where('name', 'like', '%Creative%')->orWhere('name', 'like', '%jasa%')->first() ?? $category;

        if ($admin && $category) {
            $biz1 = Business::firstOrCreate([
                'name' => 'Sample Venture',
            ], [
                'user_id' => $admin->id,
                'category_id' => $category->id,
                'slug' => Str::slug('Sample Venture'),
                'description' => 'A wonderful sample business venture for all entrepreneurs.',
                'is_visible' => true,
                'is_featured' => true,
                'type' => 'entrepreneur',
                'operational_status' => 'active',
                'offering_type' => 'product',
                'phone_number' => '08123456789',
                'address' => 'Jl. Jendral Sudirman No. 12',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
            ]);

            // Create some products
            $biz1->products()->firstOrCreate([
                'name' => 'Signature T-Shirt',
            ], [
                'description' => 'High quality premium cotton t-shirt.',
                'price' => 149000,
                'sort_order' => 1,
            ]);

            $biz1->products()->firstOrCreate([
                'name' => 'Premium Hoodie',
            ], [
                'description' => 'Comfortable and warm premium hoodie.',
                'price' => 299000,
                'sort_order' => 2,
            ]);

            // Add additional owners / members in the pivot table
            if ($studentUser) {
                $biz1->members()->syncWithoutDetaching([
                    $studentUser->id => ['position' => 'Co-Owner'],
                ]);
            }
        }

        if ($studentUser && $category) {
            $biz2 = Business::firstOrCreate([
                'name' => 'EcoFresh Catering',
            ], [
                'user_id' => $studentUser->id,
                'category_id' => $category->id,
                'slug' => Str::slug('EcoFresh Catering'),
                'description' => 'Providing healthy and eco-friendly catering services for schools and events.',
                'is_visible' => true,
                'is_featured' => false,
                'type' => 'entrepreneur',
                'operational_status' => 'active',
                'offering_type' => 'service',
                'phone_number' => '08234567890',
                'address' => 'Jl. Kebon Jeruk No. 5',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
            ]);

            // Create some services
            $biz2->products()->firstOrCreate([
                'name' => 'Daily Student Meal Box',
            ], [
                'description' => 'Balanced nutrition daily meal box for active students.',
                'price' => 35000,
                'sort_order' => 1,
            ]);

            $biz2->products()->firstOrCreate([
                'name' => 'Event Buffet Service',
            ], [
                'description' => 'Premium healthy buffet setup for events.',
                'price' => 125000,
                'sort_order' => 2,
            ]);

            // Add additional owners / members in the pivot table
            if ($budiUser) {
                $biz2->members()->syncWithoutDetaching([
                    $budiUser->id => ['position' => 'Partner & Head of Operations'],
                ]);
            }
        }

        if ($budiUser && $techCategory) {
            $biz3 = Business::firstOrCreate([
                'name' => 'Budi Solutions',
            ], [
                'user_id' => $budiUser->id,
                'category_id' => $techCategory->id,
                'slug' => Str::slug('Budi Solutions'),
                'description' => 'Modern digital marketing and SEO consultancy services.',
                'is_visible' => true,
                'is_featured' => false,
                'type' => 'intrapreneur',
                'operational_status' => 'active',
                'offering_type' => 'service',
                'phone_number' => '08111222333',
                'address' => 'Jl. Gajah Mada No. 44',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
            ]);

            // Create some services
            $biz3->products()->firstOrCreate([
                'name' => 'SEO Audit & Optimization',
            ], [
                'description' => 'Complete technical SEO audit and site performance enhancement.',
                'price' => 2500000,
                'sort_order' => 1,
            ]);

            $biz3->products()->firstOrCreate([
                'name' => 'Monthly Digital Marketing',
            ], [
                'description' => 'Social media management and targeted advertising.',
                'price' => 5000000,
                'sort_order' => 2,
            ]);

            // Add additional owners / members in the pivot table
            if ($sitiUser) {
                $biz3->members()->syncWithoutDetaching([
                    $sitiUser->id => ['position' => 'Lead Marketing Specialist'],
                ]);
            }
        }

        if ($sitiUser && $creativeCategory) {
            $biz4 = Business::firstOrCreate([
                'name' => 'Siti Creatives',
            ], [
                'user_id' => $sitiUser->id,
                'category_id' => $creativeCategory->id,
                'slug' => Str::slug('Siti Creatives'),
                'description' => 'Premium graphic design, brand identity, and content marketing.',
                'is_visible' => true,
                'is_featured' => true,
                'type' => 'entrepreneur',
                'operational_status' => 'active',
                'offering_type' => 'product',
                'phone_number' => '08556677889',
                'address' => 'Jl. Merdeka No. 10',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
            ]);

            // Create some products
            $biz4->products()->firstOrCreate([
                'name' => 'Brand Identity Package',
            ], [
                'description' => 'Complete visual guidelines, logos, and typography guidelines.',
                'price' => 4500000,
                'sort_order' => 1,
            ]);

            $biz4->products()->firstOrCreate([
                'name' => 'Custom UI/UX Website Design',
            ], [
                'description' => 'Bespoke modern UI/UX design prototype for web and mobile.',
                'price' => 6000000,
                'sort_order' => 2,
            ]);

            // Add additional owners / members in the pivot table
            if ($agusUser) {
                $biz4->members()->syncWithoutDetaching([
                    $agusUser->id => ['position' => 'Creative Director'],
                ]);
            }
        }
    }
}
