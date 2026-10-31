<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'academic_groups',
            'academic_levels',
            'academic_subjects',
            'academic_topics',
            'academic_subtopics',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                // Use $tableName in the closure to avoid shadowing
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'uuid')) {
                        $table->string('uuid', 26)->nullable()->after('id');
                    }
                });

                // Add unique index safely using try/catch instead of dummy column checks
                try {
                    DB::statement("ALTER TABLE {$tableName} ADD UNIQUE INDEX {$tableName}_uuid_unique (uuid)");
                } catch (\Exception $e) {
                    // Index may already exist, safely ignore
                }
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'academic_groups',
            'academic_levels',
            'academic_subjects',
            'academic_topics',
            'academic_subtopics',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Pass the string $tableName, not the Blueprint $table object
                    if (Schema::hasColumn($tableName, 'uuid')) {
                        $table->dropColumn('uuid');
                    }

                    // Properly clean up the unique index if it exists
                    try {
                        $table->dropUnique($tableName . '_uuid_unique');
                    } catch (\Exception $e) {
                        // Index might not exist, safely ignore
                    }
                });
            }
        }
    }
};
