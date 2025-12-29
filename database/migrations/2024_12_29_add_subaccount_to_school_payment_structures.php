<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_payment_structures', function (Blueprint $table) {
            // Add subaccount_id column to link payment to a specific subaccount
            $table->foreignId('subaccount_id')
                ->nullable()
                ->after('payment_type')
                ->constrained('subaccounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('school_payment_structures', function (Blueprint $table) {
            $table->dropForeignIdFor('Subaccount::class');
            $table->dropColumn('subaccount_id');
        });
    }
};
