<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exam_templates', function (Blueprint $table) {
            $table->foreignId('mock_exam_id')
                ->nullable()
                ->after('user_id')
                ->constrained('mock_exams')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mock_exam_templates', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\MockExam\Models\MockExam::class);
            $table->dropColumn('mock_exam_id');
        });
    }
};
