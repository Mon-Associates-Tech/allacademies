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
            if (!Schema::hasColumn('books', 'status')) {
                $table->string('status')->nullable();
            }
            if (!Schema::hasColumn('books', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('books', 'is_free')) {
                $table->boolean('is_free')->default(false);
            }
            if (!Schema::hasColumn('books', 'price')) {
                $table->decimal('price')->nullable();
            }
            if (!Schema::hasColumn('books', 'cover_image')) {
                $table->string('cover_image')->nullable();
            }
            if (!Schema::hasColumn('books', 'content_url')) {
                $table->string('content_url')->nullable();
            }
            if (!Schema::hasColumn('books', 'publication_date')) {
                $table->date('publication_date')->nullable();
            }
            if (!Schema::hasColumn('books', 'language')) {
                $table->string('language')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('description');
            $table->dropColumn('is_free');
            $table->dropColumn('price');
            $table->dropColumn('cover_image');
            $table->dropColumn('content_url');
            $table->dropColumn('publication_date');
            $table->dropColumn('language');
            $table->dropColumn('pages');
        });
    }
};
