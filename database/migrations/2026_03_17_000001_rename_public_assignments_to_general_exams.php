<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop foreign keys and indexes tied to old column names
        Schema::table('public_assignment_questions', function (Blueprint $table) {
            $table->dropForeign(['public_assignment_id']);
            $table->dropForeign(['public_assignment_section_id']);
            //$table->dropIndex('public_assignment_questions_public_assignment_id_order_index');
           // $table->dropIndex('public_assignment_questions_public_assignment_section_id_order_index');
        });

        Schema::table('public_assignment_sections', function (Blueprint $table) {
            $table->dropForeign(['public_assignment_id']);
           // $table->dropIndex('public_assignment_sections_public_assignment_id_order_index');
        });

        Schema::table('public_assignment_submissions', function (Blueprint $table) {
            $table->dropForeign(['public_assignment_id']);
           // $table->dropIndex('pub_assign_sub_status_idx');
        });

        Schema::table('proctoring_sessions', function (Blueprint $table) {
            $table->dropForeign(['public_assignment_submission_id']);
           // $table->dropIndex('proctoring_sessions_public_assignment_submission_id_index');
        });

        // Rename tables
        Schema::rename('public_assignments', 'general_exams');
        Schema::rename('public_assignment_participants', 'general_exam_participants');
        Schema::rename('public_assignment_questions', 'general_exam_questions');
        Schema::rename('public_assignment_sections', 'general_exam_sections');
        Schema::rename('public_assignment_submissions', 'general_exam_submissions');

        // Rename columns
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->renameColumn('public_assignment_id', 'general_exam_id');
        });

        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->renameColumn('public_assignment_id', 'general_exam_id');
            $table->renameColumn('public_assignment_section_id', 'general_exam_section_id');
        });

        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->renameColumn('public_assignment_id', 'general_exam_id');
        });

        Schema::table('proctoring_sessions', function (Blueprint $table) {
            $table->renameColumn('public_assignment_submission_id', 'general_exam_submission_id');
        });

        // Recreate foreign keys and indexes with new names
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->foreign('general_exam_id')
                ->references('id')
                ->on('general_exams')
                ->cascadeOnDelete();
            $table->index(['general_exam_id', 'order']);
        });

        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->foreign('general_exam_id')
                ->references('id')
                ->on('general_exams')
                ->cascadeOnDelete();
            $table->foreign('general_exam_section_id')
                ->references('id')
                ->on('general_exam_sections')
                ->cascadeOnDelete();
            $table->index(['general_exam_id', 'order']);
            $table->index(['general_exam_section_id', 'order']);
        });

        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->foreign('general_exam_id')
                ->references('id')
                ->on('general_exams')
                ->cascadeOnDelete();
            $table->index(['general_exam_id', 'status'], 'gen_exam_sub_status_idx');
        });

        Schema::table('proctoring_sessions', function (Blueprint $table) {
            $table->foreign('general_exam_submission_id')
                ->references('id')
                ->on('general_exam_submissions')
                ->cascadeOnDelete();
            $table->index('general_exam_submission_id');
        });
    }

    public function down(): void
    {
        // Drop new foreign keys and indexes
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->dropForeign(['general_exam_id']);
            $table->dropForeign(['general_exam_section_id']);
            $table->dropIndex('general_exam_questions_general_exam_id_order_index');
            $table->dropIndex('general_exam_questions_general_exam_section_id_order_index');
        });

        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->dropForeign(['general_exam_id']);
            $table->dropIndex('general_exam_sections_general_exam_id_order_index');
        });

        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropForeign(['general_exam_id']);
            $table->dropIndex('gen_exam_sub_status_idx');
        });

        Schema::table('proctoring_sessions', function (Blueprint $table) {
            $table->dropForeign(['general_exam_submission_id']);
            $table->dropIndex('proctoring_sessions_general_exam_submission_id_index');
        });

        // Rename columns back
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->renameColumn('general_exam_id', 'public_assignment_id');
        });

        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->renameColumn('general_exam_id', 'public_assignment_id');
            $table->renameColumn('general_exam_section_id', 'public_assignment_section_id');
        });

        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->renameColumn('general_exam_id', 'public_assignment_id');
        });

        Schema::table('proctoring_sessions', function (Blueprint $table) {
            $table->renameColumn('general_exam_submission_id', 'public_assignment_submission_id');
        });

        // Rename tables back
        Schema::rename('general_exam_submissions', 'public_assignment_submissions');
        Schema::rename('general_exam_sections', 'public_assignment_sections');
        Schema::rename('general_exam_questions', 'public_assignment_questions');
        Schema::rename('general_exam_participants', 'public_assignment_participants');
        Schema::rename('general_exams', 'public_assignments');

        // Restore original foreign keys and indexes
        Schema::table('public_assignment_sections', function (Blueprint $table) {
            $table->foreign('public_assignment_id')
                ->references('id')
                ->on('public_assignments')
                ->cascadeOnDelete();
            $table->index(['public_assignment_id', 'order']);
        });

        Schema::table('public_assignment_questions', function (Blueprint $table) {
            $table->foreign('public_assignment_id')
                ->references('id')
                ->on('public_assignments')
                ->cascadeOnDelete();
            $table->foreign('public_assignment_section_id')
                ->references('id')
                ->on('public_assignment_sections')
                ->cascadeOnDelete();
            $table->index(['public_assignment_id', 'order']);
            $table->index(['public_assignment_section_id', 'order']);
        });

        Schema::table('public_assignment_submissions', function (Blueprint $table) {
            $table->foreign('public_assignment_id')
                ->references('id')
                ->on('public_assignments')
                ->cascadeOnDelete();
            $table->index(['public_assignment_id', 'status'], 'pub_assign_sub_status_idx');
        });

        Schema::table('proctoring_sessions', function (Blueprint $table) {
            $table->foreign('public_assignment_submission_id')
                ->references('id')
                ->on('public_assignment_submissions')
                ->cascadeOnDelete();
            $table->index('public_assignment_submission_id');
        });
    }
};
