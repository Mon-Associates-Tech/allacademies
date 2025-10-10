<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_group_id');
            $table->unsignedBigInteger('academic_level_id');
            $table->unsignedBigInteger('current_term_id');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->string('payment_method')->default('Momo'); // e.g., Momo, Paystack
            $table->unsignedBigInteger('school_id');
            $table->foreign('academic_group_id')->references('id')->on('academic_groups')->onDelete('cascade');
            $table->foreign('academic_level_id')->references('id')->on('academic_levels')->onDelete('cascade');
            $table->foreign('current_term_id')->references('id')->on('academic_periods')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_fee_structures');
    }
};
