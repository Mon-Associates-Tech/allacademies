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
        Schema::table('teachers', static function (Blueprint $table) {
            $table->foreignId('academic_group_id')
                ->nullable()
                ->after('id')
                ->constrained('academic_groups')
                ->nullOnDelete()
                ->comment('Foreign key to the academic groups table');

            $table->foreignId('academic_level_id')
                ->nullable()
                ->after('id')
                ->constrained('academic_levels')
                ->nullOnDelete()
                ->comment('Foreign key to the academic levels table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', static function (Blueprint $table) {
            $table->dropForeign(['academic_group_id']);
            $table->dropColumn('academic_group_id');
            $table->dropForeign(['academic_level_id']);
            $table->dropColumn('academic_level_id');
        });
    }
};
