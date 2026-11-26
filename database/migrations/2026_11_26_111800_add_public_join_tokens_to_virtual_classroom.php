<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->string('join_token', 64)->nullable()->unique();
        });

        DB::table('virtual_sessions')->orderBy('id')->chunkById(100, function ($sessions) {
            foreach ($sessions as $session) {
                DB::table('virtual_sessions')
                    ->where('id', $session->id)
                    ->update(['join_token' => Str::random(64)]);
            }
        });

        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->string('join_token', 64)->nullable(false)->change();
        });

        Schema::table('virtual_session_participants', function (Blueprint $table) {
            $table->dropUnique(['virtual_session_id', 'user_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('virtual_session_participants', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['virtual_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        DB::table('virtual_session_participants')->whereNull('user_id')->delete();

        Schema::table('virtual_session_participants', function (Blueprint $table) {
            $table->dropUnique(['virtual_session_id', 'user_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('virtual_session_participants', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['virtual_session_id', 'user_id']);
        });

        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->dropUnique(['join_token']);
            $table->dropColumn('join_token');
        });
    }
};