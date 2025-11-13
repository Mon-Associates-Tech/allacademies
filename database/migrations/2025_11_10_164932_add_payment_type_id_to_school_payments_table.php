<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_structure_id')->nullable()->after('school_id')->index();

            $table->foreign('payment_structure_id')
                ->references('id')
                ->on('school_payments')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->dropForeign(['payment_structure_id']);
            $table->dropColumn('payment_structure_id');
        });
    }
};
