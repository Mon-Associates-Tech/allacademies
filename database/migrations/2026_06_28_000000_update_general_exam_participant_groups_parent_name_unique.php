<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_participant_groups', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['parent_id', 'name'], 'gep_groups_parent_name_uq');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_participant_groups', function (Blueprint $table) {
            $table->dropUnique('gep_groups_parent_name_uq');
            $table->unique('name');
        });
    }
};
