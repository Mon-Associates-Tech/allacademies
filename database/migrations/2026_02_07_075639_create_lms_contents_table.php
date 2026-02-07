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
        if (! Schema::hasTable('lms_contents')) {
            Schema::create('lms_contents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('section_id')->constrained('lms_sections')->cascadeOnDelete();
                $table->string('type'); // video, audio, text, quiz, feedback
                $table->string('title');
                $table->longText('content')->nullable(); // For text content
                $table->string('media_path')->nullable(); // For video/audio files
                $table->string('media_url')->nullable(); // For external video/audio URLs
                $table->integer('duration_seconds')->nullable(); // For video/audio duration
                $table->integer('word_count')->nullable(); // For text content
                $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();
                $table->integer('order')->default(0);
                $table->boolean('is_required')->default(true);
                $table->json('completion_criteria')->nullable(); // e.g., {"watch_percentage": 90, "min_score": 70}
                $table->json('ai_summary')->nullable(); // AI-generated summary
                $table->boolean('is_published')->default(true);
                $table->timestamps();

                $table->index('section_id');
                $table->index('type');
                $table->index('order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_contents');
    }
};
