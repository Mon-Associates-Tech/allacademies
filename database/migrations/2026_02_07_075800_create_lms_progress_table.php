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
        if (! Schema::hasTable('lms_progress')) {
            Schema::create('lms_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enrollment_id')->constrained('lms_enrollments')->cascadeOnDelete();
                $table->foreignId('content_id')->constrained('lms_contents')->cascadeOnDelete();
                $table->boolean('is_completed')->default(false);
                $table->integer('progress_value')->default(0); // e.g., seconds watched, paragraphs read
                $table->integer('progress_max')->default(100); // e.g., total seconds, total paragraphs
                $table->decimal('quiz_score', 5, 2)->nullable();
                $table->boolean('quiz_passed')->nullable();
                $table->integer('quiz_attempts')->default(0);
                $table->json('interaction_data')->nullable(); // Detailed tracking data
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('last_accessed_at')->nullable();
                $table->timestamps();

                $table->unique(['enrollment_id', 'content_id']);
                $table->index('is_completed');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_progress');
    }
};
