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
        Schema::table('assignment_sections', function (Blueprint $table) {
            $table->integer('duration_minutes')->nullable()->after('instructions');
            $table->enum('grading_mode', ['manual', 'automatic'])->default('automatic')->after('marks_per_question');
            $table->string('grading_source')->nullable()->after('grading_mode'); // 'question_answer' or 'marking_scheme_path'
            $table->text('marking_scheme')->nullable()->after('grading_source'); // text or path to marking scheme
        });
    }

    public function down(): void
    {
        Schema::table('assignment_sections', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'grading_mode', 'grading_source', 'marking_scheme']);
        });
    }
};
