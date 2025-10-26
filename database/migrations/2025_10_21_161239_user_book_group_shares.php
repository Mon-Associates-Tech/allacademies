<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_book_group_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_book_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('shareable_type'); // AcademicGroup, AcademicLevel, etc.
            $table->unsignedBigInteger('shareable_id');
            $table->timestamp('shared_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['shareable_type', 'shareable_id']);
            $table->unique(['user_book_id', 'shareable_type', 'shareable_id'], 'unique_group_share');
        });

        // Add index to user_book_shares for better query performance
        Schema::table('user_book_shares', function (Blueprint $table) {
            $table->index(['user_book_id', 'status']);
            $table->index(['shared_to_user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_book_group_shares');

        Schema::table('user_book_shares', function (Blueprint $table) {
            $table->dropIndex(['user_book_id', 'status']);
            $table->dropIndex(['shared_to_user_id', 'status']);
        });
    }
};
