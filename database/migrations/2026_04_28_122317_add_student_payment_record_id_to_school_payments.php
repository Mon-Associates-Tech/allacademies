<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->foreignId('student_payment_record_id')->nullable()->after('payment_structure_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('school_payments', function (Blueprint $table) {
            $table->dropForeign(['student_payment_record_id']);
            $table->dropColumn('student_payment_record_id');
        });
    }
};
