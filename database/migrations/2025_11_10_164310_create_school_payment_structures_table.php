<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_payment_structures', function (Blueprint $table) {
            $table->id();

            // School context
            $table->unsignedBigInteger('school_id')->index();

            // Academic context
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();
            $table->unsignedBigInteger('academic_period_id')->nullable()->index();
            $table->unsignedBigInteger('academic_group_id')->nullable()->index();
            $table->unsignedBigInteger('academic_level_id')->nullable()->index();

            // Fee details
            $table->string('name'); // e.g., "First Term Tuition 2024"
            $table->string('payment_type')->index(); // tuition, library, etc.
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('GHS');
            $table->date('due_date')->nullable();
            $table->string('payment_period')->nullable(); // term_1, semester_1, annual, etc.

            // Settings
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('allow_partial_payment')->default(false);
            $table->decimal('minimum_partial_amount', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);

            // Additional info
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();

            // Tracking
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('set null');
            $table->foreign('academic_period_id')->references('id')->on('academic_periods')->onDelete('set null');
            $table->foreign('academic_group_id')->references('id')->on('academic_groups')->onDelete('set null');
            $table->foreign('academic_level_id')->references('id')->on('academic_levels')->onDelete('set null');

            // Indexes for queries
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['school_id', 'academic_period_id']);
            $table->index(['school_id', 'payment_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_payment_structures');
    }
};
