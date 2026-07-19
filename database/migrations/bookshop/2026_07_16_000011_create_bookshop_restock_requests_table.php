<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookshop_restock_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->constrained('bookshop_branches')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('bookshop_books')->cascadeOnDelete();
            $table->unsignedInteger('requested_quantity');

            $table->string('status')->default('pending'); // RestockRequestStatus enum

            $table->foreignId('requested_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();
            $table->foreignId('reviewed_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->string('reason')->nullable(); // rejection reason
            $table->text('notes')->nullable(); // requester's notes

            $table->timestamps();

            $table->index(['branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_restock_requests');
    }
};
