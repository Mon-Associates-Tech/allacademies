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
        Schema::create('pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'Basic' or 'Premium'
            $table->string('model');
            $table->decimal('initial_price', 10, 2); // Price for first 6 months
            $table->decimal('subsequent_price', 10, 2); // Price after initial period
            $table->bigInteger('monthly_token_limit'); // Tokens allocated per month
            $table->integer('initial_period_months')->default(6); // Number of months for initial pricing
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_tiers');
    }
};
