<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_assignment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('public_assignment_section_id')->nullable()->constrained()->onDelete('cascade');

            // Question type: multiple_choice, true_false, short_answer, essay
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
            $table->text('question');
            $table->text('explanation')->nullable();

            // For multiple choice questions - stored as JSON array
            $table->json('options')->nullable();

            // Correct answer - for MCQ: option key (A, B, C, D, E), for true/false: 'true'/'false', for short answer: expected text
            $table->text('correct_answer')->nullable();

            // For essay questions - rubric/grading criteria for AI grading
            $table->text('grading_rubric')->nullable();
            $table->json('keywords')->nullable();

            // Scoring
            $table->integer('marks')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');

            // Ordering
            $table->integer('order')->default(0);

            // AI generation metadata
            $table->boolean('ai_generated')->default(false);
            $table->boolean('is_edited')->default(false);

            $table->timestamps();

            $table->index(['public_assignment_id', 'order']);
            // $table->index(['public_assignment_section_id', 'order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_assignment_questions');
    }
};
