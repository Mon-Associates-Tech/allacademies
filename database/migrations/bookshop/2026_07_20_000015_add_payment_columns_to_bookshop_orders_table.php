<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookshop_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('bookshop_orders', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status'); // PaymentStatus enum
            }
            if (! Schema::hasColumn('bookshop_orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_status'); // Paystack transaction reference
            }
            if (! Schema::hasColumn('bookshop_orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_reference');
            }
        });

        try {
            Schema::table('bookshop_orders', function (Blueprint $table) {
                $table->index('payment_status');
            });
        } catch (\Throwable) {}
    }

    public function down(): void
    {
        Schema::table('bookshop_orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropColumn(['payment_status', 'payment_reference', 'paid_at']);
        });
    }
};
