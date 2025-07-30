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
        Schema::table('book_reviews', function (Blueprint $table) {
            $table->text('author_reply')->nullable()->after('review');
            $table->timestamp('author_replied_at')->nullable()->after('author_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_reviews', function (Blueprint $table) {
            $table->dropColumn(['author_reply', 'author_replied_at']);
        });
    }
};
