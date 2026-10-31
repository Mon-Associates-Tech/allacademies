<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('multiple_choice_questions')) {
            Schema::table('multiple_choice_questions', function (Blueprint $table) {
                if (!Schema::hasColumn('multiple_choice_questions', 'uuid')) {
                    $table->string('uuid', 26)->nullable()->after('id');
                }
            });

            try {
                \DB::statement("ALTER TABLE multiple_choice_questions ADD UNIQUE INDEX multiple_choice_questions_uuid_unique (uuid)");
            } catch (\Exception $e) {
                // Index may already exist
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('multiple_choice_questions')) {
            Schema::table('multiple_choice_questions', function (Blueprint $table) {
                if (Schema::hasColumn('multiple_choice_questions', 'uuid')) {
                    $table->dropColumn('uuid');
                }
            });
        }
    }
};
