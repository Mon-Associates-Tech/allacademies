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
            $table->decimal('amount', 10, 2)->nullable()->after('pricing_tier_id')->comment('Total purchase amount for this subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_token_subscriptions', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
