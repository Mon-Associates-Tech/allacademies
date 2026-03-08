<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_assignment_id')->constrained()->onDelete('cascade');

            // Polymorphic participant - can be student or public_assignment_participant
            $table->string('participant_type')->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);

            // Responses stored as JSON: [{question_id, response, is_correct, points_earned, ai_feedback}]
            $table->json('responses')->nullable();

            // Scoring
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();

            // Grading status
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'auto_graded', 'manually_reviewed', 'final'])->default('not_started');
            $table->boolean('requires_manual_review')->default(false);
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');

            // Teacher feedback
            $table->text('teacher_feedback')->nullable();

            // Proctoring violation tracking
            $table->integer('tab_switch_count')->default(0);
            $table->json('violations')->nullable();
            $table->boolean('auto_submitted')->default(false);
            $table->string('auto_submit_reason')->nullable();

            // Attempt tracking
            $table->integer('attempt_number')->default(1);

            // IP and browser info
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index(['public_assignment_id', 'status'], 'pub_assign_sub_status_idx');
            $table->index(['participant_type', 'participant_id'], 'pub_assign_sub_participant_idx');
            $table->index('submitted_at', 'pub_assign_sub_submitted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_assignment_submissions');
    }
};
