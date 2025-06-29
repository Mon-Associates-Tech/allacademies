<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_academic_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('assignment_academic_level', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_level_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('assignment_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('assignment_student_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('assignment_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_topic_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('assignment_subtopic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_subtopic_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_subtopic');
        Schema::dropIfExists('assignment_topic');
        Schema::dropIfExists('assignment_student_group');
        Schema::dropIfExists('assignment_student');
        Schema::dropIfExists('assignment_academic_level');
        Schema::dropIfExists('assignment_academic_group');
    }
};
