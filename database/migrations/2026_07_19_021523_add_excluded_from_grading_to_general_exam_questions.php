<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `excluded_from_grading` to `general_exam_questions`.
 *
 * When true, the question is skipped during grading and every submission
 * receives full marks for it (i.e. the question is nullified post-exam).
 * A bulk regrade is dispatched whenever this flag is toggled.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->boolean('excluded_from_grading')
                  ->default(false)
                  ->after('order')
                  ->comment('When true, question is nullified: all submissions receive full marks for it.');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->dropColumn('excluded_from_grading');
        });
    }
};
