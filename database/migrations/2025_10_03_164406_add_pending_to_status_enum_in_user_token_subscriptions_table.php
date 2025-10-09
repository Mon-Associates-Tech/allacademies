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
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'depleted', 'pending', 'expired'])->change()->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'depleted', 'expired'])->change();
        });
    }
};
