<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->string('participant_name')->nullable()->after('participant_id');
            $table->string('participant_email')->nullable()->after('participant_name');
            $table->integer('time_taken_minutes')->nullable()->after('time_spent_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropColumn(['participant_name', 'participant_email', 'time_taken_minutes']);
        });
    }
};
