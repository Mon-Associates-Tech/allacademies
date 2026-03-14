<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_assignments', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('access_code')
                ->constrained()
                ->onDelete('cascade');
        });

        // Drop existing FK to allow nulls
        Schema::table('public_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });

        // Make teacher_id nullable (driver-specific to avoid doctrine/dbal)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE public_assignments MODIFY teacher_id BIGINT UNSIGNED NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE public_assignments ALTER COLUMN teacher_id DROP NOT NULL');
        }

    }

    public function down(): void
    {
        Schema::table('public_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropForeign(['teacher_id']);
        });

        // Revert teacher_id nullability (best-effort; will fail if nulls remain)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE public_assignments MODIFY teacher_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE public_assignments ALTER COLUMN teacher_id SET NOT NULL');
        }

        Schema::table('public_assignments', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }
};
