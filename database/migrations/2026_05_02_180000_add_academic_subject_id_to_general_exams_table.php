<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->foreignId('academic_subject_id')
                ->nullable()
                ->after('general_exam_subscription_id')
                ->constrained('academic_subjects')
                ->nullOnDelete();
            $table->index(['general_exam_subscription_id', 'academic_subject_id'], 'ge_subscription_subject_idx');
        });
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropIndex('ge_subscription_subject_idx');
            $table->dropConstrainedForeignId('academic_subject_id');
        });
    }
};
