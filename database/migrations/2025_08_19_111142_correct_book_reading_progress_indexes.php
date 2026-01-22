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
        Schema::table('book_reading_progress', function (Blueprint $table) {
            try {
                $table->dropForeign(['book_id']);
            } catch (Exception $e) {
                // Foreign key might not exist
            }

            try {
                $table->dropUnique(['book_id']);
            } catch (Exception $e) {
                // Index might not exist
            }

            try {
                $table->unique(['book_id', 'user_id'], 'book_user_unique_progress');
            } catch (Exception $e) {
                // Unique constraint might already exist
            }

            try {
                $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            } catch (Exception $e) {
                // Foreign key might already exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_reading_progress', function (Blueprint $table) {
            try {
                $table->dropUnique('book_user_unique_progress');
            } catch (Exception $e) {
                // Handle if constraint doesn't exist
            }

            try {
                $table->dropForeign(['book_id']);
            } catch (Exception $e) {
                // Handle if constraint doesn't exist
            }

            try {
                $table->unique('book_id');
            } catch (Exception $e) {
                // Handle if constraint already exists
            }

            try {
                $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            } catch (Exception $e) {
                // Handle if constraint already exists
            }
        });
    }
};
