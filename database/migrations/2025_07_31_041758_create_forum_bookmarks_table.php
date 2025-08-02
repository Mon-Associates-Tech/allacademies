<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->string('bookmarkable_type');
            $table->unsignedBigInteger('bookmarkable_id');
            $table->unsignedBigInteger('user_id');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['bookmarkable_type', 'bookmarkable_id']);
            $table->unique(['bookmarkable_type', 'bookmarkable_id', 'user_id'], 'unique_bookmark');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_bookmarks');
    }
};
