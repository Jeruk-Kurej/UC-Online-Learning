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
            $table->string('ai_sentiment')->nullable()->comment('Sentiment from AI analysis');
            $table->decimal('ai_score', 5, 2)->nullable()->comment('AI assigned score for testimony');
            $table->text('ai_rejection_reason')->nullable()->comment('Reason for rejection by AI');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ai_sentiment', 'ai_score', 'ai_rejection_reason']);
        });
    }
};
