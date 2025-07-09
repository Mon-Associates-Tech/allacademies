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
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('assignment_id')->nullable()->after('book_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['self', 'assignment'])->default('self')->after('assignment_id');
            $table->json('question_types')->nullable()->after('type');
            $table->integer('time_limit_minutes')->nullable()->after('end_time');
            $table->boolean('has_essay_questions')->default(false)->after('time_limit_minutes');
            $table->enum('essay_grading_status', ['pending', 'in_progress', 'completed'])->nullable()->after('has_essay_questions');
            $table->foreignId('graded_by')->nullable()->after('essay_grading_status')->constrained('teachers')->onDelete('set null');
            $table->timestamp('graded_at')->nullable()->after('graded_by');
            $table->text('teacher_feedback')->nullable()->after('graded_at');

            // Update existing status column
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'pending_review', 'graded'])->default('not_started')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropForeign(['graded_by']);
            $table->dropColumn([
                'assignment_id', 'type', 'question_types', 'time_limit_minutes',
                'has_essay_questions', 'essay_grading_status', 'graded_by',
                'graded_at', 'teacher_feedback'
            ]);
        });
    }
};
