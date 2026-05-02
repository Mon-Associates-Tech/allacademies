<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_score_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('general_exam_submission_id');
            $table->foreign('general_exam_submission_id', 'gesa_submission_id_foreign')
                ->references('id')->on('general_exam_submissions')->cascadeOnDelete();
            $table->foreignId('edited_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('old_score', 8, 2)->nullable();
            $table->decimal('new_score', 8, 2)->nullable();
            $table->string('old_grade', 10)->nullable();
            $table->string('new_grade', 10)->nullable();
            $table->decimal('old_percentage', 5, 2)->nullable();
            $table->decimal('new_percentage', 5, 2)->nullable();
            $table->text('reason')->nullable();
            $table->json('question_changes')->nullable()->comment('Per-question score changes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_score_audit_logs');
    }
};
