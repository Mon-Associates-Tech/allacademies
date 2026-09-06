<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the table exists before creating it
        if (!Schema::hasTable('book_borrowings')) {
            Schema::create('book_borrowings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('book_id')->constrained()->onDelete('cascade');
                $table->timestamp('borrow_date')->nullable();
                $table->timestamp('due_date')->nullable();
                $table->timestamp('return_date')->nullable();
                $table->string('status')->nullable(); // borrowed, returned, overdue
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Check if the table exists before dropping it
        if (Schema::hasTable('book_borrowings')) {
            Schema::dropIfExists('book_borrowings');
        }
    }
};
