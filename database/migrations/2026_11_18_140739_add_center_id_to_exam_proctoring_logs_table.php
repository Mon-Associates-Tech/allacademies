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
        if (!Schema::hasColumn('exam_proctoring_logs', 'center_id')) {
            Schema::table('exam_proctoring_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('center_id')->nullable()->after('general_exam_submission_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_proctoring_logs', function (Blueprint $table) {
            $table->dropColumn('center_id');
        });
    }
};
