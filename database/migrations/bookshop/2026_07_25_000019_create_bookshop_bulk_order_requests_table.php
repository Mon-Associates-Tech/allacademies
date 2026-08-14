<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookshop_bulk_order_requests')) {
            Schema::create('bookshop_bulk_order_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_number')->unique();
 
                $table->foreignId('customer_id')->constrained('bookshop_customers')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()
                ->constrained('bookshop_branches')->nullOnDelete();
 
                // Self-declared, free text - deliberately NOT a foreign key to
                // the host app's School/AcademicGroup models. This module
                // stays standalone/extractable, same as every other part of
                // BookShop; "institutional" here means the buyer told us who
                // they're ordering for, not that we've integrated with the
                // host app's academic hierarchy.
                $table->string('institution_name');
                $table->string('institution_type')->default('other'); // school|corporate|church|ngo|other
                $table->string('contact_phone')->nullable();
                $table->date('requested_delivery_date')->nullable();
 
                $table->string('status')->default('pending'); // BulkOrderRequestStatus enum
                $table->text('notes')->nullable(); // from the customer, at submission
                $table->text('staff_notes')->nullable(); // from staff, alongside a quote
 
                $table->foreignId('reviewed_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();
                $table->timestamp('quoted_at')->nullable();
                $table->timestamp('reviewed_at')->nullable(); // accepted/rejected/cancelled timestamp
                $table->string('rejection_reason')->nullable();
 
                // Set once accepted and converted into a real Order, so the
                // request keeps a permanent link to what it became.
                $table->foreignId('order_id')->nullable()
                ->constrained('bookshop_orders')->nullOnDelete();
 
                $table->timestamps();
 
                $table->index(['branch_id', 'status']);
                $table->index(['customer_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_bulk_order_requests');
    }
};
