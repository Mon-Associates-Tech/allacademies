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
        Schema::create('subscription_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pricing_tier_id')->constrained('pricing_tiers')->onDelete('cascade');
            $table->integer('cycle_number'); // 1 for first month, 2 for second, etc.
            $table->dateTime('cycle_start_date');
            $table->dateTime('cycle_end_date');
            $table->bigInteger('tokens_allocated'); // Monthly token limit from pricing tier
            $table->bigInteger('tokens_used')->default(0);
            $table->decimal('current_price', 10, 2); // Price for this specific cycle
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'cycle_start_date']);
            $table->index('cycle_end_date');
            $table->unique(['user_id', 'cycle_number']); // One cycle per month per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_cycles');
    }
};
