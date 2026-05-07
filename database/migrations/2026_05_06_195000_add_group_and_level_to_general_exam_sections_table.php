<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->foreignId('academic_group_id')->nullable()->after('question_count')->constrained('academic_groups')->nullOnDelete();
            $table->foreignId('academic_level_id')->nullable()->after('academic_group_id')->constrained('academic_levels')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_level_id');
            $table->dropConstrainedForeignId('academic_group_id');
        });
    }
};

