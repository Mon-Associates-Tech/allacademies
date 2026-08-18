<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->mediumText('question')->change();
            $table->mediumText('explanation')->nullable()->change();
            $table->mediumText('correct_answer')->nullable()->change();
            $table->mediumText('grading_rubric')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->text('question')->change();
            $table->text('explanation')->nullable()->change();
            $table->text('correct_answer')->nullable()->change();
            $table->text('grading_rubric')->nullable()->change();
        });
    }
};
