<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('examhub_admin_messages')) {
            Schema::create('examhub_admin_messages', function (Blueprint $table) {
                $table->id();
                
                // Foreign keys
                $table->unsignedBigInteger('general_exam_id');
                $table->unsignedBigInteger('general_exam_submission_id');
                $table->unsignedBigInteger('sent_by')->nullable();
                
                // Message details
                $table->enum('message_type', ['warning', 'message', 'terminate', 'force_submit', 'extend_time'])->default('message');
                $table->text('message_content');
                $table->json('metadata')->nullable(); // For additional context like time extension minutes
                
                // Timestamps
                $table->timestamp('sent_at')->useCurrent();
                $table->timestamp('acknowledged_at')->nullable();
                
                $table->timestamps();
                
                // Foreign key constraints
                $table->foreign('general_exam_id', 'ehb_admin_msg_exam_fk')
                      ->references('id')
                      ->on('general_exams')
                      ->onDelete('cascade');
                      
                $table->foreign('general_exam_submission_id', 'ehb_admin_msg_sub_fk')
                      ->references('id')
                      ->on('general_exam_submissions')
                      ->onDelete('cascade');
                      
                $table->foreign('sent_by', 'ehb_admin_msg_sent_by_fk')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
                
                // Indexes for performance
                $table->index(['general_exam_id', 'sent_at']);
                $table->index('general_exam_submission_id');
                $table->index('message_type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('examhub_admin_messages');
    }
};
