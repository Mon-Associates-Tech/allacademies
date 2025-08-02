<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_mentions', function (Blueprint $table) {
            $table->id();
            $table->string('mentionable_type');
            $table->unsignedBigInteger('mentionable_id');
            $table->unsignedBigInteger('mentioned_user_id');
            $table->unsignedBigInteger('mentioning_user_id');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('mentioned_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mentioning_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['mentionable_type', 'mentionable_id']);
            $table->index(['mentioned_user_id', 'is_read']);
            $table->unique(['mentionable_type', 'mentionable_id', 'mentioned_user_id', 'mentioning_user_id'], 'unique_mention');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_mentions');
    }
};
