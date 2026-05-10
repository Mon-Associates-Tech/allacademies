<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->foreignId('academic_subject_id')->nullable()->after('question_count')->constrained('academic_subjects')->nullOnDelete();
            $table->json('topic_ids')->nullable()->after('academic_subject_id');
            $table->json('subtopic_ids')->nullable()->after('topic_ids');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_subject_id');
            $table->dropColumn(['topic_ids', 'subtopic_ids']);
        });
    }
};

