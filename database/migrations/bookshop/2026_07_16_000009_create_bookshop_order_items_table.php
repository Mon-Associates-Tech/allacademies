<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookshop_order_items')) {
            Schema::create('bookshop_order_items', function (Blueprint $table) {
                $table->id();
 
                $table->foreignId('order_id')->constrained('bookshop_orders')->cascadeOnDelete();
 
                // Nullable + set null on delete: the snapshot columns below keep
                // the line item meaningful even if the catalog entry is removed.
                $table->foreignId('book_id')->nullable()
                ->constrained('bookshop_books')->nullOnDelete();
 
                $table->string('title_snapshot');
                $table->string('author_snapshot')->nullable();
                $table->decimal('unit_price', 10, 2);
                $table->unsignedInteger('quantity');
                $table->decimal('line_total', 10, 2);
 
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_order_items');
    }
};
