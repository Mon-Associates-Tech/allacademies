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
        Schema::create('school_payments', function (Blueprint $table) {
            $table->id();

            // School context
            $table->unsignedBigInteger('school_id')->index();

            // Student information
            $table->unsignedBigInteger('student_id')->index();

            // Academic context
            $table->unsignedBigInteger('academic_group_id')->nullable()->index();
            $table->unsignedBigInteger('academic_level_id')->nullable()->index();
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();
            $table->unsignedBigInteger('academic_period_id')->nullable()->index();

            // Payment details
            $table->string('payment_type')->index(); // e.g., 'tuition', 'library', 'transport', 'uniform', 'exam', 'other'
            $table->decimal('amount', 15, 2);
            $table->decimal('fixed_amount', 15, 2)->nullable(); // The original/expected amount
            $table->string('currency', 10)->default('GHS');
            $table->string('payment_period')->nullable(); // e.g., 'term_1', 'annual', 'monthly'

            // Payer information (who initiated the payment)
            $table->string('payer_type')->index(); // 'parent', 'student', 'other'
            $table->unsignedBigInteger('payer_id')->nullable(); // User ID if parent/student
            $table->string('payer_name')->nullable(); // For 'other' payers
            $table->string('payer_email')->nullable();
            $table->string('payer_phone')->nullable();

            // Payment status and tracking
            $table->string('status')->default('pending')->index(); // 'pending', 'succeeded', 'failed', 'cancelled'
            $table->string('reference')->unique();
            $table->string('transaction_id')->nullable()->index(); // Gateway transaction ID
            $table->string('payment_method')->nullable(); // 'paystack', 'cash', 'bank_transfer', etc.

            // Gateway integration
            $table->string('gateway')->nullable(); // 'paystack', 'flutterwave', etc.
            $table->text('authorization_url')->nullable();
            $table->json('gateway_response')->nullable();

            // Additional metadata
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // For additional flexible data
            $table->timestamp('paid_at')->nullable();

            // Tracking
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for reporting and filtering
            $table->index(['school_id', 'payment_type', 'status']);
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['school_id', 'academic_period_id']);
            $table->index(['created_at', 'status']);

            // Foreign key constraints
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('academic_group_id')->references('id')->on('academic_groups')->onDelete('set null');
            $table->foreign('academic_level_id')->references('id')->on('academic_levels')->onDelete('set null');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('set null');
            $table->foreign('academic_period_id')->references('id')->on('academic_periods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_payments');
    }
};
