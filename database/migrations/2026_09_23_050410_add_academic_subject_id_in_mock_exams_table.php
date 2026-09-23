<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->foreignId('academic_subject_id')
                ->nullable()
                ->after('mock_exam_subscription_id')
                ->constrained('academic_subjects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_subject_id');
        });
    }
};
