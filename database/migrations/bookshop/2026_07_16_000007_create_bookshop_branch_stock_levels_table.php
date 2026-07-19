<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookshop_branch_stock_levels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->constrained('bookshop_branches')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('bookshop_books')->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);

            $table->foreignId('updated_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();

            $table->timestamps();

            $table->unique(['branch_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_branch_stock_levels');
    }
};
