<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('time_limit_minutes');
            $table->string('question_type')->nullable()->after('source_type');
            $table->unsignedInteger('question_count')->default(0)->after('question_type');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'question_type', 'question_count']);
        });
    }
};

