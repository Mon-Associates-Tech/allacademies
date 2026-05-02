<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('general_exam_subscription_id');
            $table->foreign('general_exam_subscription_id', 'gesp_subscription_id_foreign')
                ->references('id')->on('general_exam_subscriptions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('paystack_reference')->unique()->nullable();
            $table->string('paystack_access_code')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('GHS');
            $table->string('status')->default('pending')->comment('pending, success, failed');
            $table->string('payment_type')->default('new')->comment('new, topup');
            $table->unsignedSmallInteger('additional_participants')->default(0)->comment('For topup: extra slots purchased');
            $table->json('paystack_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_subscription_payments');
    }
};
