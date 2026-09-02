<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exam_subject_exams', function (Blueprint $table) {
            // Add template_id to track which template was used to create this subject exam
            $table->unsignedBigInteger('template_id')->nullable()->after('mock_exam_id');
            $table->foreign('template_id')->references('id')->on('mock_exam_templates')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mock_exam_subject_exams', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });
    }
};