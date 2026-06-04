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
        Schema::create('exam_admin_messages', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->unsignedBigInteger('general_exam_id');
            $table->unsignedBigInteger('general_exam_submission_id');
            $table->unsignedBigInteger('sent_by'); // Admin who sent the message
            
            // Message content
            $table->enum('message_type', ['warning', 'info', 'termination', 'force_submit', 'time_extension'])->default('info');
            $table->text('message');
            $table->json('metadata')->nullable(); // Additional context (e.g., extension minutes, termination details)
            
            // Delivery tracking
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('general_exam_id', 'eam_gen_exam_fk')
                  ->references('id')
                  ->on('general_exams')
                  ->onDelete('cascade');
            
            $table->foreign('general_exam_submission_id', 'eam_gen_exam_sub_fk')
                  ->references('id')
                  ->on('general_exam_submissions')
                  ->onDelete('cascade');
            
            $table->foreign('sent_by', 'eam_sent_by_fk')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['general_exam_id', 'created_at']);
            $table->index('general_exam_submission_id');
            $table->index('message_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_admin_messages');
    }
};
