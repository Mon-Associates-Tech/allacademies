<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_exam_submission_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mock_exam_section_id')->constrained()->cascadeOnDelete();

            // Reference back to the source question bank (non-FK – source may be deleted)
            $table->unsignedBigInteger('source_question_id')->nullable();
            $table->string('source_type');          // 'multiple_choice' | 'true_false' | 'essay'

            // Denormalised content snapshot
            $table->text('question_text');
            $table->json('options')->nullable();            // {A: '...', B: '...', ...}
            $table->text('correct_answer')->nullable();     // MCQ letter / 'true' / 'false'
            $table->text('answer_explanation')->nullable(); // essay model answer
            $table->json('answer_keywords')->nullable();    // essay keyword list

            $table->decimal('marks', 6, 2)->default(1.00);
            $table->unsignedSmallInteger('order')->default(0);
            $table->string('difficulty_level')->nullable();
            $table->timestamps();

            // Indexes for fast lookups
            $table->index('mock_exam_submission_id');
            $table->index('mock_exam_section_id');
            $table->unique(['mock_exam_submission_id', 'mock_exam_section_id', 'source_question_id', 'source_type'], 'submission_section_source_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_exam_submission_questions');
    }
};
