<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('book_subscriptions', 'reference')) {
                $table->string('reference')->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('book_subscriptions', 'annual_fee')) {
                $table->decimal('annual_fee', 8, 2)->nullable()->after('reference');
            }
            if (!Schema::hasColumn('book_subscriptions', 'payment_completed_at')) {
                $table->timestamp('payment_completed_at')->nullable()->after('annual_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('book_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['reference', 'annual_fee', 'payment_completed_at']);
        });
    }
};
