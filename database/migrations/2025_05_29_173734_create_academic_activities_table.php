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
        Schema::create('academic_activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('activity_type'); // 'assessment', 'book_reading', 'group_meeting', 'quiz', 'exam', etc.
            $table->foreignId('subject_id')->nullable()->constrained()->on('academic_subjects')->nullOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->string('status')->default('scheduled'); // 'scheduled', 'in_progress', 'completed', 'cancelled'
            $table->boolean('is_group_activity')->default(false);
            $table->foreignId('group_id')->nullable()->constrained('student_groups')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->json('metadata')->nullable(); // JSON field for additional type-specific data
            $table->timestamps();

            // Add indexes for fields frequently used in queries
            $table->index('activity_type');
            $table->index('start_time');
            $table->index('end_time');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_activities');
    }
};
