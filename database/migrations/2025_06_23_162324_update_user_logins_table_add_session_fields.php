<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_logins', function (Blueprint $table) {
            if (!Schema::hasColumn('user_logins', 'login_at')) {
                $table->timestamp('login_at')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('user_logins', 'logout_at')) {
                $table->timestamp('logout_at')->nullable()->after('login_at');
            }
            if (!Schema::hasColumn('user_logins', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('logout_at');
            }
            if (!Schema::hasColumn('user_logins', 'logout_type')) {
                $table->enum('logout_type', ['manual', 'session_timeout', 'forced', 'browser_close'])->nullable()->after('duration_minutes');
            }

            // Add indexes for better performance
            $table->index(['user_id']);
            $table->index(['login_at']);
            $table->index(['logout_at']);
            $table->index(['session_id']);
        });
    }

    public function down()
    {
        Schema::table('user_logins', static function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['login_at']);
            $table->dropIndex(['logout_at']);
            $table->dropIndex(['session_id']);

            $table->dropColumn(['login_at', 'logout_at', 'duration_minutes', 'logout_type']);
        });
    }
};
