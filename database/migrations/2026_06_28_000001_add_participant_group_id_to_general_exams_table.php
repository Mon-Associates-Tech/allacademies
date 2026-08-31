<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->foreignId('participant_group_id')
                ->nullable()
                ->constrained('general_exam_participant_groups')
                ->nullOnDelete()
                ->after('configured_match_mode');
        });
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropForeign(['participant_group_id']);
            $table->dropColumn('participant_group_id');
        });
    }
};
