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
        Schema::create('report_card_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_card_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('academic_subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->decimal('assessments_score', 5, 2)->nullable(); // 10%
            $table->decimal('quizzes_score', 5, 2)->nullable(); // 30%
            $table->decimal('final_exam_score', 5, 2)->nullable(); // 60%
            $table->decimal('total_score', 5, 2)->nullable();
            $table->string('grade_label')->nullable(); // A+, A, B, etc.
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_card_grades');
    }
};
