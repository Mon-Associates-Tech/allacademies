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
        // Check if the table exists before creating it
        if (!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->foreignId('author_id')->constrained()->onDelete('cascade');
                $table->foreignId('book_category_id')->constrained()->onDelete('cascade');
                $table->string('edition')->nullable();
                $table->string('publisher')->nullable();
                $table->integer('pages')->nullable();
                $table->boolean('has_hardcopy')->default(false);
                $table->boolean('has_softcopy')->default(false);
                $table->text('additional_info')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the table exists before dropping it
        if (Schema::hasTable('books')) {
            Schema::dropIfExists('books');
        }
    }
};
