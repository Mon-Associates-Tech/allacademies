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
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_free')->default(false);
            $table->decimal('price')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('content_url')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('language')->nullable();
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
