<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reviewer_name'); // Store name for display purposes
            $table->string('reviewer_email')->nullable(); // Optional email
            $table->unsignedTinyInteger('rating')->comment('Rating from 1 to 5');
            $table->string('title')->nullable(); // Review title/summary
            $table->text('review'); // Main review content
            $table->boolean('is_verified_purchase')->default(false); // Whether reviewer actually purchased/subscribed
            $table->boolean('is_approved')->default(true); // For moderation
            $table->json('helpful_votes')->nullable(); // Store user IDs who found this helpful
            $table->unsignedInteger('helpful_count')->default(0); // Cache helpful votes count
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['book_id', 'is_approved']);
            $table->index(['user_id', 'book_id']);
            $table->index(['rating', 'created_at']);
            $table->index('is_verified_purchase');

            // Ensure one review per user per book
            $table->unique(['book_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
    }
};
