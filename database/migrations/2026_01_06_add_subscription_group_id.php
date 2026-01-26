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
        Schema::table('subscription_cycles', function (Blueprint $table) {
            // Add subscription_group_id to group cycles from same purchase
            $table->uuid('subscription_group_id')->nullable()->after('pricing_tier_id');
            // Index for fast lookups of cycles in a group
            $table->index('subscription_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_cycles', function (Blueprint $table) {
            $table->dropIndex(['subscription_group_id']);
            $table->dropColumn('subscription_group_id');
        });
    }
};
