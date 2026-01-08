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
            // Add topup_tokens_allocated to track tokens purchased as topups separately
            // This allows us to:
            // 1. Deduct from base allocation first, then from topup
            // 2. Carry over only topup tokens when cycle expires (not base allocation)
            $table->bigInteger('topup_tokens_allocated')->default(0)->after('tokens_allocated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_cycles', function (Blueprint $table) {
            $table->dropColumn('topup_tokens_allocated');
        });
    }
};
