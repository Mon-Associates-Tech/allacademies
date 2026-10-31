<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'center_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('center_id')->nullable()->after('id');
                $table->index('center_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['users_center_id']);
            $table->dropColumn('center_id');
        });
    }
};
