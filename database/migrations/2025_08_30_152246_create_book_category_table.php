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
        Schema::create('book_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->on('book_categories')->onDelete('cascade');

            $table->unique(['book_id', 'category_id']);
            $table->timestamps();
        });

        $books = DB::table('books')->whereNotNull('book_category_id')->get();
        foreach ($books as $book) {
            DB::table('book_category')->insert([
                'book_id' => $book->id,
                'category_id' => $book->book_category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'book_category_id')) {
                try {
                    $table->dropForeign(['book_category_id']);
                } catch (Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('book_category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // 1. Add category_id back to books table
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
            if (! $exists) {
                DB::table('books')
                    ->where('id', $bc->book_id)
                    ->update(['book_category_id' => $bc->category_id]);
            }
        }

        // 3. Drop the pivot table
        Schema::dropIfExists('book_category');
    }
};
