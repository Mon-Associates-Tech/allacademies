<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_exam_subject_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_group_id')->constrained();
            $table->foreignId('academic_level_id')->constrained();
            $table->foreignId('academic_subject_id')->constrained();
            $table->string('title')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->unsignedSmallInteger('duration_in_minutes')->nullable();
            $table->json('topic_ids')->nullable();
            $table->json('subtopic_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_exam_subject_exams');
    }
};
