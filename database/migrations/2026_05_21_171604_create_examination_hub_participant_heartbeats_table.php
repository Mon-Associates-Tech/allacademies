<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('examhub_participant_heartbeats')) {
            Schema::create('examhub_participant_heartbeats', function (Blueprint $table) {
                $table->id();
                
                // Define foreign key columns explicitly first
                $table->unsignedBigInteger('general_exam_id');
                $table->unsignedBigInteger('general_exam_submission_id');
                
                // Participant info (denormalized for quick access)
                $table->string('participant_name')->nullable();
                $table->string('participant_email')->nullable();

                // Session tracking
                $table->string('session_token', 64)->unique();
                $table->timestamp('last_heartbeat_at')->nullable();
                $table->timestamp('started_at')->nullable();

                // Activity tracking
                $table->enum('status', ['active', 'idle', 'away', 'disconnected', 'completed', 'terminated'])->default('active');
                $table->boolean('is_focused')->default(true);
                $table->unsignedInteger('current_question_index')->default(0);
                $table->unsignedInteger('current_section_index')->default(0);
                $table->unsignedInteger('questions_answered')->default(0);
                $table->unsignedInteger('total_questions')->default(0);

                // Violation tracking (denormalized for live display)
                $table->unsignedInteger('violation_count')->default(0);
                $table->unsignedInteger('high_severity_count')->default(0);
                $table->unsignedInteger('medium_severity_count')->default(0);
                $table->boolean('is_flagged')->default(false);

                // Browser/device info
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->string('browser')->nullable();
                $table->string('os')->nullable();

                // Admin actions
                $table->boolean('has_warning')->default(false);
                $table->text('admin_message')->nullable();
                $table->timestamp('warned_at')->nullable();
                
                $table->unsignedBigInteger('terminated_by')->nullable();
                $table->timestamp('terminated_at')->nullable();
                $table->string('termination_reason')->nullable();

                $table->timestamps();

                // Add foreign key constraints with custom names
                $table->foreign('general_exam_id', 'ehb_part_hb_gen_exam_fk')
                      ->references('id')
                      ->on('general_exams')
                      ->onDelete('cascade');

                $table->foreign('general_exam_submission_id', 'ehb_part_gen_exam_sub_fk')
                      ->references('id')
                      ->on('general_exam_submissions')
                      ->onDelete('cascade');

                $table->foreign('terminated_by', 'ehb_part_hb_term_by_fk')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');

                // Indexes for performance
                $table->index(['general_exam_id', 'status']);
                $table->index('last_heartbeat_at');
                $table->index('session_token');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('examhub_participant_heartbeats');
    }
};