<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates a table for storing predefined mock exam templates that administrators
     * can use to quickly generate exams without manual configuration.
     */
    public function up(): void
    {
        Schema::create('mock_exam_templates', function (Blueprint $table) {
            $table->id();
            
            // Owner of the template
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Academic hierarchy - what this template applies to
            $table->foreignId('academic_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_subject_id')->constrained()->cascadeOnDelete();
            
            // Template metadata
            $table->string('name')->comment('Template name for easy identification');
            $table->text('description')->nullable()->comment('Optional description of the template');
            $table->boolean('is_active')->default(true)->comment('Whether template is available for use');
            
            // Default duration in minutes for exams created from this template
            $table->unsignedSmallInteger('default_duration_minutes')->nullable();
            
            // Topics and subtopics filter (stored as JSON arrays)
            $table->json('topic_ids')->nullable()->comment('Array of topic IDs to filter questions');
            $table->json('subtopic_ids')->nullable()->comment('Array of subtopic IDs to filter questions');
            
            // Section configurations (stored as JSON array of section objects)
            // Each section object contains: title, instructions, question_type, question_count, marks_per_question, is_randomized
            $table->json('sections_config')->comment('Array of section configurations');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['academic_subject_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_exam_templates');
    }
};
