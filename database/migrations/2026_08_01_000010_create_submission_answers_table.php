<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_submission_answers', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 26)->unique();

            // Added ->name() with a short identifier (< 64 chars)
            $table->foreignId('general_exam_submission_id')
                ->constrained('general_exam_submissions')
                ->cascadeOnDelete()
                ->name('gesa_ges_id_foreign');

            // Added ->name() with a short identifier (< 64 chars)
            $table->foreignId('general_exam_question_id')
                ->constrained('general_exam_questions')
                ->cascadeOnDelete()
                ->name('gesa_geq_id_foreign');

            $table->string('question_uuid', 26);
            $table->string('response', 8); // Single letter answer: A, B, C, D, E, or True/False
            $table->timestamp('answered_at')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_earned', 8, 2)->nullable();
            $table->unsignedInteger('graded_revision')->nullable();

            // One answer per submission per question
            $table->unique(['general_exam_submission_id', 'general_exam_question_id'], 'submission_question_unique');
            $table->index('question_uuid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_submission_answers');
    }
};
