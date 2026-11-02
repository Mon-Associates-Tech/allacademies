<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_exam_participant_groups', 'academic_subject_id')) {
            Schema::table('general_exam_participant_groups', function (Blueprint $table) {
                $table->unsignedBigInteger('academic_subject_id')->nullable()->after('parent_id');
                $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->nullOnDelete();
                $table->index('academic_subject_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('general_exam_participant_groups', function (Blueprint $table) {
            $table->dropForeign(['academic_subject_id']);
            $table->dropIndex(['academic_subject_id']);
            $table->dropColumn('academic_subject_id');
        });
    }
};
