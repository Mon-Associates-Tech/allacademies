<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->json('topic_ids')->nullable()->after('is_randomized');
            $table->json('subtopic_ids')->nullable()->after('topic_ids');
        });
    }

    public function down(): void
    {
        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->dropColumn(['topic_ids', 'subtopic_ids']);
        });
    }
};