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
        Schema::table('openai_token_usage_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_cycle_id')->nullable()->after('subscription_id');
            $table->index('subscription_cycle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('openai_token_usage_logs', function (Blueprint $table) {
            $table->dropColumn('subscription_cycle_id');
            $table->dropIndex('subscription_cycle_id');
        });
    }
};
