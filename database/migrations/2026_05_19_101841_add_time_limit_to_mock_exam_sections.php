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
        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->unsignedSmallInteger('time_limit_minutes')->nullable()->after('marks_per_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->dropColumn('time_limit_minutes');
        });
    }
};
