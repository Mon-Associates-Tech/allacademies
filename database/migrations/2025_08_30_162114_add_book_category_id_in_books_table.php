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
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('book_category_id')
                ->nullable()
                ->constrained()
                ->on('book_categories')
                ->onDelete('cascade');
        });

        // 2. Restore category_id values from book_category pivot
        $bookCategories = DB::table('book_category')->get();

        foreach ($bookCategories as $bc) {
            // if book already has a category_id, skip (keeps first one only)
            $exists = DB::table('books')->where('id', $bc->book_id)->value('book_category_id');
            if (!$exists) {
                DB::table('books')
                    ->where('id', $bc->book_id)
                    ->update(['book_category_id' => $bc->category_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            //
        });
    }
};
