<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookshop_bulk_order_request_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bulk_order_request_id')->constrained('bookshop_bulk_order_requests')->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()
                ->constrained('bookshop_books')->nullOnDelete();

            // Snapshotted so the request stays meaningful even if the
            // catalog entry changes/deactivates later - same rationale
            // as OrderItem's title/author snapshot.
            $table->string('title_snapshot');

            $table->unsignedInteger('requested_quantity');

            // Null until staff quotes it - deliberately separate from the
            // catalog price (bulk orders are exactly where a volume
            // discount matters) and separate from requested_quantity,
            // since staff may only be able to fulfill part of a request.
            $table->decimal('quoted_unit_price', 10, 2)->nullable();
            $table->unsignedInteger('quoted_quantity')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_bulk_order_request_items');
    }
};
