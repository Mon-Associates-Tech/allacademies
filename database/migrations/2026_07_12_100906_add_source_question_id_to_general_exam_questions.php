<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `source_question_id` to `general_exam_questions`.
 *
 * WHY THIS COLUMN EXISTS
 * ──────────────────────
 * Questions are currently persisted as full content copies with no link back
 * to the originating row in `multiple_choice_questions` (or other question-
 * bank tables).  This makes it impossible to:
 *   • propagate answer-key corrections from the bank to live exam questions
 *   • re-grade submissions after an answer correction
 *   • de-duplicate questions across exams
 *
 * This column stores the PK of the source row (always from
 * `multiple_choice_questions` for MCQ questions; null for AI-generated or
 * manually created questions).  No FK constraint is defined because:
 *   1. Multiple question types could be source tables.
 *   2. Deleting a bank question should not cascade-delete exam content.
 *
 * BACK-FILL
 * ─────────
 * Run:  php artisan exam:backfill-question-sources
 * This performs a best-effort text-match between existing exam questions
 * and `multiple_choice_questions`, populating the column for historic data.
 *
 * GOING FORWARD
 * ─────────────
 * ExamQuestionPersistenceService now reads `source_question_id` from the
 * incoming question data array and stores it here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('source_question_id')
                  ->nullable()
                  ->after('is_edited')
                  ->comment('PK of the originating row in the question bank (e.g. multiple_choice_questions.id). NULL = AI-generated or manually created.');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->dropColumn('source_question_id');
        });
    }
};
