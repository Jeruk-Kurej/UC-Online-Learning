<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('type')->default('product')->after('business_id');
            $table->string('price_type')->default('fixed')->after('price');
        });

        // Update existing products based on business offering_type
        try {
            $products = \Illuminate\Support\Facades\DB::table('products')->get();
            foreach ($products as $product) {
                $business = \Illuminate\Support\Facades\DB::table('businesses')
                    ->where('id', $product->business_id)
                    ->first();
                if ($business && $business->offering_type === 'service') {
                    \Illuminate\Support\Facades\DB::table('products')
                        ->where('id', $product->id)
                        ->update(['type' => 'service']);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if tables or columns don't exist yet in certain test run environments
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['type', 'price_type']);
        });
    }
};
