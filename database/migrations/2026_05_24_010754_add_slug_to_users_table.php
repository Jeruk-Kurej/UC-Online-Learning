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
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Populate slugs for existing users
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $slug = \Illuminate\Support\Str::slug($user->name);
            $original = $slug;
            $i = 1;
            while (\App\Models\User::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $i++;
            }
            $user->slug = $slug;
            $user->save();
        }

        // Make slug unique
        Schema::table('users', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
