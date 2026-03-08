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
            $table->foreignId('pricing_tier_id')->nullable()->after('package_id')->constrained('pricing_tiers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            $table->dropForeignKey(['pricing_tier_id']);
            $table->dropColumn('pricing_tier_id');
        });
    }
};
