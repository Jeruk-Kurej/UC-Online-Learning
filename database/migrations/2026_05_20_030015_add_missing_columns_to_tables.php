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
            $table->text('cv_url')->nullable()->after('profile_photo_url')->comment('CSV: Curriculum Vitae');
            $table->text('expertise_certification_url')->nullable()->after('activities_doc_url')->comment('CSV: Expertise Certification');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('level_position')->nullable()->after('position')->comment('CSV: Level Position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cv_url', 'expertise_certification_url']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('level_position');
        });
    }
};
