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
        if (!Schema::hasColumn('general_exam_participant_group_members', 'auth')) {
            Schema::table('general_exam_participant_group_members', function (Blueprint $table) {
                $table->string('auth', 20)->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_exam_participant_group_members', function (Blueprint $table) {
            $table->dropColumn('auth');
        });
    }
};
