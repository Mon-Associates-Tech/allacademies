<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all indexes on the table
        $indexes = DB::select("SHOW INDEX FROM book_reading_progress WHERE Key_name = 'book_reading_progress_book_id_unique'");

        if (!empty($indexes)) {
            // If the problematic unique index exists, we need to drop it
            Schema::table('book_reading_progress', function (Blueprint $table) {
                try {
                    // First drop foreign key constraint
                    $table->dropForeign('book_reading_progress_book_id_foreign');
                } catch (Exception $e) {
                    // Foreign key might not exist or have different name
                }

                try {
                    // Then drop the unique index
                    $table->dropUnique('book_reading_progress_book_id_unique');
                } catch (Exception $e) {
                    // Index might not exist or have different name
                }

                // Add the correct unique constraint
                $table->unique(['book_id', 'user_id'], 'book_user_unique_progress');

                // Re-add the foreign key constraint
                $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_reading_progress', function (Blueprint $table) {
            try {
                // Drop the correct unique constraint
                $table->dropUnique('book_user_unique_progress');
            } catch (Exception $e) {
                // Handle if constraint doesn't exist
            }

            try {
                // Drop foreign key constraint
                $table->dropForeign('book_reading_progress_book_id_foreign');
            } catch (Exception $e) {
                // Handle if constraint doesn't exist
            }

            // Re-add the incorrect unique constraint on book_id only
            $table->unique('book_id');

            // Re-add the foreign key constraint
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }
};
