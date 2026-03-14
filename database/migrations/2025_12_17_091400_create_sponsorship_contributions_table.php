<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsorship_program_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sponsor_offer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payer_name')->nullable();
            $table->string('payer_email');
            $table->string('payer_phone')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->boolean('sponsor_covers_fee')->default(false);
            $table->decimal('total_charged', 12, 2);
            $table->decimal('net_amount', 12, 2); // Amount benefactor receives
            $table->string('currency')->default('GHS');
            $table->string('status')->default('pending'); // pledged, pending, completed, failed, refunded
            $table->string('payment_reference')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('authorization_url')->nullable();
            $table->json('paystack_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_contributions');
    }
};
