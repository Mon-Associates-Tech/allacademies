<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_exam_questions', 'source_question_uuid')) {
            Schema::table('general_exam_questions', function (Blueprint $table) {
                $table->string('source_question_uuid', 26)->nullable()->after('source_question_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->dropColumn('source_question_uuid');
        });
    }
};
