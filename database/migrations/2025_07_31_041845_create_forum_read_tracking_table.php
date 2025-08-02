<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_read_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('forum_topic_id');
            $table->unsignedBigInteger('last_read_post_id')->nullable();
            $table->timestamp('last_read_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('forum_topic_id')->references('id')->on('forum_topics')->onDelete('cascade');
            $table->foreign('last_read_post_id')->references('id')->on('forum_posts')->onDelete('set null');

            $table->unique(['user_id', 'forum_topic_id']);
            $table->index(['forum_topic_id', 'last_read_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_read_tracking');
    }
};
