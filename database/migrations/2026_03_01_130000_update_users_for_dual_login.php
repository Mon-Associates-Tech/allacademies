<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('email');
            }

            // Make email nullable to support username-only accounts
            if (Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->change();
            }

            if (! Schema::hasColumn('users', 'login_type')) {
                $table->enum('login_type', ['email', 'username'])->default('email')->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'login_type')) {
                $table->dropColumn('login_type');
            }

            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique('users_username_unique');
                $table->dropColumn('username');
            }

            if (Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable(false)->change();
                $table->unique('email');
            }
        });
    }
};
