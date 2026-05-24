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
        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'business_challenge')) {
                $table->dropColumn('business_challenge');
            }
            if (Schema::hasColumn('businesses', 'business_challenges')) {
                $table->dropColumn('business_challenges');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->text('business_challenge')->nullable();
        });
    }
};
