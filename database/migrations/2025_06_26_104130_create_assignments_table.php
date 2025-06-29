<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'examination'])->default('quiz');
            $table->foreignId('academic_subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->integer('duration_in_minutes');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_randomized')->default(false);
            $table->enum('status', ['draft', 'published', 'completed'])->default('draft');
            $table->text('instructions')->nullable();
            $table->integer('total_marks')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
