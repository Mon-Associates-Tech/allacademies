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
            // Make package_id nullable to support the new pricing_tier_id system
            $table->unsignedBigInteger('package_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
        });
    }
};
