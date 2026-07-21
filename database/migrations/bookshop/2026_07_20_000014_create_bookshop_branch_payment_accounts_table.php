<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookshop_branch_payment_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->unique()
                ->constrained('bookshop_branches')->cascadeOnDelete();

            $table->string('subaccount_code')->nullable();
            $table->string('business_name')->nullable();
            $table->string('settlement_bank')->nullable(); // display name, e.g. "GCB Bank"
            $table->string('bank_code')->nullable();        // Paystack's numeric bank code
            $table->string('account_number')->nullable();

            // Platform's cut of each transaction on this subaccount, as a
            // percentage (0-100). Mirrors Paystack's own percentage_charge
            // semantics: this is what the PLATFORM keeps, the branch gets
            // the remainder settled directly by Paystack.
            $table->decimal('percentage_charge', 5, 2)->default(0);

            $table->json('paystack_response')->nullable();
            $table->boolean('is_active')->default(false);

            $table->foreignId('updated_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_branch_payment_accounts');
    }
};
