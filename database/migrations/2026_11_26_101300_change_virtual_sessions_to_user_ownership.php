<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('school_id')->constrained()->cascadeOnDelete();
        });

        DB::table('virtual_sessions')
            ->whereNotNull('teacher_id')
            ->update([
                'user_id' => DB::raw('(SELECT user_id FROM teachers WHERE teachers.id = virtual_sessions.teacher_id)'),
            ]);

        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropIndex(['teacher_id', 'scheduled_start']);
            $table->dropColumn('teacher_id');
            $table->index(['user_id', 'scheduled_start']);
        });
    }

    public function down(): void
    {
        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->after('school_id')->constrained()->cascadeOnDelete();
            $table->index(['teacher_id', 'scheduled_start']);
        });

        DB::table('virtual_sessions')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->each(function ($session) {
                $teacherId = DB::table('teachers')
                    ->where('user_id', $session->user_id)
                    ->value('id');

                if ($teacherId) {
                    DB::table('virtual_sessions')
                        ->where('id', $session->id)
                        ->update(['teacher_id' => $teacherId]);
                }
            });

        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'scheduled_start']);
            $table->dropColumn('user_id');
        });
    }
};