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
        if (!Schema::hasColumn('general_exam_participant_groups', 'uuid')) {
            Schema::table('general_exam_participant_groups', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_exam_participant_groups', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
