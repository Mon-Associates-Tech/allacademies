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
        Schema::create('school_fees', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys and relationships
            $table->unsignedBigInteger('school_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            
            // Polymorphic relationship (payer can be parent, student, etc.)
            $table->unsignedBigInteger('payer_id')->nullable();
            $table->string('payer_type')->nullable();
            
            $table->unsignedBigInteger('term_id')->index();

            // Fee details
            $table->string('school_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('term_total_amount', 15, 2)->nullable();
            $table->string('currency', 10)->default('GHC');

            // Payment info
            $table->string('status')->default('pending');
            $table->string('reference')->unique();
            $table->string('authorization_url')->nullable();
            $table->json('paystack_response')->nullable();

            // Timestamps
            $table->timestamps();

            // Optional: foreign key constraints (uncomment if related tables exist)
            // $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            // $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            // $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_fees');
    }
};
