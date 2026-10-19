<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exam_subject_exams', function (Blueprint $table) {
            $table->json('layout_config')->nullable()->after('subtopic_ids');
            $table->boolean('uses_random_questions')->default(false)->after('layout_config');
        });
    }

    public function down(): void
    {
        Schema::table('mock_exam_subject_exams', function (Blueprint $table) {
            $table->dropColumn(['layout_config', 'uses_random_questions']);
        });
    }
};
