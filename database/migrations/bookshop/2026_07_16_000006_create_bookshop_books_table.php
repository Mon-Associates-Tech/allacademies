<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookshop_books', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->nullable()
                ->constrained('bookshop_categories')->nullOnDelete();

            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();

            $table->timestamps();

            $table->index('title');
            $table->index('isbn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_books');
    }
};
