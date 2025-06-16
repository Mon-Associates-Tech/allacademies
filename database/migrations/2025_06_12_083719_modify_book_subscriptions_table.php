<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_subscriptions', function (Blueprint $table) {
            $table->string('reference')->nullable()->unique()->after('status');
            $table->decimal('annual_fee', 8, 2)->nullable()->after('reference');
            $table->timestamp('payment_completed_at')->nullable()->after('annual_fee');
        });
    }

    public function down(): void
    {
        Schema::table('book_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['reference', 'annual_fee', 'payment_completed_at']);
        });
    }
};
