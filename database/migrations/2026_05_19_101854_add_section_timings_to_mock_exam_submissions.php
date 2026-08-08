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
        Schema::table('mock_exam_submissions', function (Blueprint $table) {
            $table->json('section_timings')->nullable()->after('randomized_question_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mock_exam_submissions', function (Blueprint $table) {
            $table->dropColumn('section_timings');
        });
    }
};
