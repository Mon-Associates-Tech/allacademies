<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_book_shares', function (Blueprint $table) {
            // Make shared_to_user_id nullable since group shares don't need it
            $table->unsignedBigInteger('shared_to_user_id')->nullable()->change();

            // Also make shared_to_email nullable for consistency
            $table->string('shared_to_email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_book_shares', function (Blueprint $table) {
            // Revert back to NOT NULL (this may fail if there are null values)
            $table->unsignedBigInteger('shared_to_user_id')->nullable(false)->change();
            $table->string('shared_to_email')->nullable(false)->change();
        });
    }
};
