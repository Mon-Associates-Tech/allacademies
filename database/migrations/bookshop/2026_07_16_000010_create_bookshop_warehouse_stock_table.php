<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookshop_warehouse_stock')) {
            Schema::create('bookshop_warehouse_stock', function (Blueprint $table) {
                $table->id();
 
                $table->foreignId('book_id')->unique()
                ->constrained('bookshop_books')->cascadeOnDelete();
 
                $table->unsignedInteger('quantity')->default(0);
 
                $table->foreignId('updated_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();
 
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_warehouse_stock');
    }
};
