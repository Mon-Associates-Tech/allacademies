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
            // Change action_type enum to include 'topup' option
            $table->enum('action_type', ['trial', 'purchase', 'upgrade', 'downgrade', 'topup'])
                ->default('purchase')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            // Revert to original enum values (without 'topup')
            $table->enum('action_type', ['trial', 'purchase', 'upgrade', 'downgrade'])
                ->default('purchase')
                ->change();
        });
    }
};
