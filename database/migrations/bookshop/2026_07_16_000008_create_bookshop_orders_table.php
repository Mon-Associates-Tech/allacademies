<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookshop_orders')) {
            Schema::create('bookshop_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
 
                $table->foreignId('customer_id')->constrained('bookshop_customers')->cascadeOnDelete();
 
                // Nullable: an order could theoretically be created before branch
                // resolution completes, though the current OrderPlacementService
                // always resolves synchronously and blocks on failure.
                $table->foreignId('branch_id')->nullable()
                ->constrained('bookshop_branches')->nullOnDelete();
 
                $table->string('status')->default('pending'); // OrderStatus enum
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->text('notes')->nullable();
                $table->string('cancelled_reason')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('completed_at')->nullable();
 
                $table->timestamps();
 
                $table->index(['branch_id', 'status']);
                $table->index(['customer_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_orders');
    }
};
