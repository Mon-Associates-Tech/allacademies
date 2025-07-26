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
        Schema::table('user_logins', function (Blueprint $table) {
            $table->string('country')->nullable();
        });
        Schema::rename('user_logins', 'login_activities');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_activities', function (Blueprint $table) {
            $table->dropColumn('country');
        });
        Schema::rename('login_activities', 'user_logins');
    }
};
