<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lessons')) {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->on('academic_subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('student_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        // Check if the table exists before dropping it
        if (Schema::hasTable('lessons')) {
            Schema::dropIfExists('lessons');
        }
    }
};
