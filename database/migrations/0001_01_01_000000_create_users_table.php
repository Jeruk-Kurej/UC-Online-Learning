<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // System / Auth
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            // CSV: Identity
            $table->timestamp('submitted_at')->nullable()->comment('CSV: Timestamp');
            $table->string('prefix_title')->nullable()->comment('CSV: Prefix Title');
            $table->string('name')->comment('CSV: Full Name');
            $table->string('suffix_title')->nullable()->comment('CSV: Suffix Title');
            $table->string('personal_email')->nullable()->comment('CSV: Personal Email Address');

            // CSV: Contact
            $table->string('phone_number')->nullable()->comment('CSV: Personal Phone Number');
            $table->string('mobile_number')->nullable()->comment('CSV: Personal Mobile Number');
            $table->string('whatsapp')->nullable()->comment('CSV: Personal WhatsApp');
            $table->string('linkedin')->nullable()->comment('CSV: LinkedIn');

            // CSV: Academic
            $table->string('current_status')->nullable()->comment('CSV: Current Status (Entrepreneur/Intrapreneur)');
            $table->string('nis')->nullable()->comment('CSV: NIS (Student ID)');
            $table->string('year_of_enrollment')->nullable()->comment('CSV: Year of Enrollment');
            $table->string('graduate_year')->nullable()->comment('CSV: Graduate Year');
            $table->string('major')->nullable()->comment('CSV: Major');

            // CSV: Profile extras
            $table->text('testimony')->nullable()->comment('CSV: Testimony');
            $table->text('cv_url')->nullable()->comment('CSV: Curriculum Vitae');
            $table->text('profile_photo_url')->nullable()->comment('CSV: Professional Profile Photo');
            $table->text('activities_doc_url')->nullable()->comment('CSV: Professional Activities Documentation');

            // Platform management
            $table->boolean('is_visible')->default(true)->comment('Hide/show student on public pages');
            $table->enum('student_status', ['active', 'inactive', 'cuti', 'alumni', 'student aktif', 'student non aktif', 'student cuti', 'alumni aktif', 'alumni non aktif', 'alumni cuti'])->default('active')->comment('Admin-only detailed status');

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
