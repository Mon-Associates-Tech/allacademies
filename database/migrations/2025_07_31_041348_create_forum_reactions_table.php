<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_reactions', function (Blueprint $table) {
            $table->id();
            $table->string('reactable_type');
            $table->unsignedBigInteger('reactable_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'like', 'dislike', 'love', 'laugh', etc.
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['reactable_type', 'reactable_id']);
            $table->index(['user_id', 'type']);
            $table->unique(['reactable_type', 'reactable_id', 'user_id'], 'unique_reaction');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_reactions');
    }
};
