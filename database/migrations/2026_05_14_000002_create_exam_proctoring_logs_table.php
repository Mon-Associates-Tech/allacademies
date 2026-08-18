<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_proctoring_logs')) {
            Schema::create('exam_proctoring_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('general_exam_submission_id')->constrained()->cascadeOnDelete();
                $table->string('event_type', 50); // tab_switch, multiple_faces, exam_exit, copy_attempt, right_click, etc.
                $table->json('event_data')->nullable();
                $table->enum('severity', ['low', 'medium', 'high'])->default('low');
                $table->timestamp('occurred_at')->useCurrent();
                $table->timestamps();

                $table->index(['general_exam_submission_id', 'event_type']);
                $table->index(['general_exam_submission_id', 'severity']);
            });
        }
    }

    public function down(): void
    {
        // dropIfExists already handles checking if the table exists before dropping
        Schema::dropIfExists('exam_proctoring_logs');
    }
};