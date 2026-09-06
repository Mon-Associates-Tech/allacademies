<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('book_approvals')) {
        Schema::create('book_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('librarian_id')->constrained()->onDelete('cascade');
            $table->string('status')->nullable(); // approved, rejected, pending
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
    }

    public function down(): void
    {
        // Check if the table exists before dropping it
        if (Schema::hasTable('book_approvals')) {
            Schema::dropIfExists('book_approvals');
        }
    }
};
