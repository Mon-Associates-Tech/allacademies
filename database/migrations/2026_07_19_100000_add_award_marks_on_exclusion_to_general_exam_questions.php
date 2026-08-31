<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->boolean('award_marks_on_exclusion')->default(false)->after('excluded_from_grading');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->dropColumn('award_marks_on_exclusion');
        });
    }
};
