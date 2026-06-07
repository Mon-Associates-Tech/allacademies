<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_exam_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_id')->constrained()->cascadeOnDelete();

            // Polymorphic-style columns (string type rather than full morph to keep it simple)
            $table->string('participant_type');           // 'general' | 'configured'
            $table->unsignedBigInteger('participant_id');

            // Cached identity fields for display without loading the participant
            $table->string('participant_name');
            $table->string('participant_email')->nullable();

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->nullable();

            // Responses & randomisation
            $table->json('responses')->nullable();
            $table->json('randomized_question_order')->nullable();

            // Grading
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->enum('status', [
                'not_started', 'in_progress', 'submitted',
                'auto_graded', 'manually_reviewed', 'final',
            ])->default('not_started');
            $table->boolean('requires_manual_review')->default(false);
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('teacher_feedback')->nullable();

            $table->unsignedSmallInteger('attempt_number')->default(1);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['mock_exam_id', 'participant_type', 'participant_id'], 'mock_exam_sub_participant_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_exam_submissions');
    }
};
