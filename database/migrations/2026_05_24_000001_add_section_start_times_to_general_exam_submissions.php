<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->json('section_start_times')->nullable()->after('randomized_question_order');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropColumn('section_start_times');
        });
    }
};
