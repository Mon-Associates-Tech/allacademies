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
            $table->timestamp('last_activity_at')->nullable()->after('submitted_at');
            $table->unsignedTinyInteger('current_section_index')->default(0)->after('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mock_exam_submissions', function (Blueprint $table) {
            $table->dropColumn(['last_activity_at', 'current_section_index']);
        });
    }
};
