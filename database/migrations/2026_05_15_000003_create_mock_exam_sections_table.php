<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_exam_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_subject_exam_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay', 'mixed'])
                  ->default('multiple_choice');
            $table->unsignedSmallInteger('question_count')->default(0);
            $table->decimal('marks_per_question', 6, 2)->default(1.00);
            $table->boolean('is_randomized')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_exam_sections');
    }
};
