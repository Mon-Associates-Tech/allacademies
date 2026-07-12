<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `target_total_marks` to `general_exams`.
 *
 * WHY THIS COLUMN EXISTS
 * ──────────────────────
 * `total_marks` on the exam is overwritten by recalculateTotalMarks(), which
 * sums every question's marks after questions are created/deleted.  When an
 * exam is intended to be out of 100 but only 60 questions were added, the
 * raw sum (60) becomes the denominator — producing scores like "45/60"
 * instead of the intended "75/100".
 *
 * `target_total_marks` stores the *intended* ceiling exactly once (during
 * exam creation / editing) and is never touched by recalculateTotalMarks().
 * The grading service uses it as the denominator when it is set.
 *
 * NULL  → fall back to the live question sum (legacy / no target configured)
 * 100   → a student answering 60/60 correctly scores 60/100 = 60%; one
 *          answering 45/60 correctly scores 45/100 = 45%.
 *
 * NOTE: the alternative "proportional" interpretation (60 questions each
 * worth 100/60 ≈ 1.667 marks so that a perfect score always equals 100) is
 * NOT used here because it makes manual grading confusing and changes the
 * meaning of per-question marks.  If that behaviour is needed in the future,
 * a separate boolean `proportional_scoring` can be added.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            // Stored after total_marks for logical grouping
            $table->unsignedSmallInteger('target_total_marks')
                  ->nullable()
                  ->after('total_marks')
                  ->comment('Intended out-of value for scoring (e.g. 100). NULL = use live question sum.');
        });
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropColumn('target_total_marks');
        });
    }
};
