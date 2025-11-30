<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add tracking column to School Fees (for tuition payments linked to aid)
        Schema::table('school_fees', function (Blueprint $table) {
            $table->foreignId('financial_aid_id')
                ->nullable()
                ->after('student_id') // adjusting position
                ->constrained('financial_aids')
                ->nullOnDelete();
        });

        // 2. Add tracking column to School Payments (for other payments linked to aid)
        Schema::table('school_payments', function (Blueprint $table) {
            $table->foreignId('financial_aid_id')
                ->nullable()
                ->after('student_id')
                ->constrained('financial_aids')
                ->nullOnDelete();
        });

        // 3. Add missing columns to Financial Aids table
        Schema::table('financial_aids', function (Blueprint $table) {
            $table->decimal('amount_raised', 15, 2)->default(0.00)->after('amount');
            // 'amount_left' and 'progress_percentage' are typically computed,
            // but we can add them if strict caching is required.
            // Usually 'amount_raised' is sufficient to calculate the rest.
        });
    }

    public function down()
    {
        Schema::table('school_fees', function (Blueprint $table) {
            $table->dropForeign(['financial_aid_id']);
            $table->dropColumn('financial_aid_id');
        });

        Schema::table('school_payments', function (Blueprint $table) {
            $table->dropForeign(['financial_aid_id']);
            $table->dropColumn('financial_aid_id');
        });

        Schema::table('financial_aids', function (Blueprint $table) {
            $table->dropColumn('amount_raised');
        });
    }
};
