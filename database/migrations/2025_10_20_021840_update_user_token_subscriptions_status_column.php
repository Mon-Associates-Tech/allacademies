<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            // Change status from enum to string to support PHP enum casting
            $table->string('status', 50)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            // Rollback would go here if needed
            $table->enum('status', ['active', 'pending', 'expired', 'depleted', 'replaced'])->default('pending')->change();
        });
    }
};
