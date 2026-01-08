<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('subaccount_id')->nullable()->after('payment_type')->index();

            $table->foreign('subaccount_id')
                ->references('id')
                ->on('subaccounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->dropForeign(['subaccount_id']);
            $table->dropColumn('subaccount_id');
        });
    }
};
