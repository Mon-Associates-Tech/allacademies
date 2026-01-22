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
        Schema::create('subaccounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('subaccount_code')->unique(); // Paystack SUB_xxxxxx
            $table->string('business_name');
            $table->string('settlement_bank');
            $table->string('account_number');
            $table->decimal('percentage_charge', 5, 2)->default(0.00); // e.g., 15.5%
            $table->string('description')->nullable();
            $table->json('paystack_response')->nullable(); // store full API response if needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subaccounts');
    }
};
