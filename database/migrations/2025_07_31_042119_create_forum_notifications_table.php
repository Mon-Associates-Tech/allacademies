<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'mention', 'reply', 'topic_update', 'moderation', etc.
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'read_at']);
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_notifications');
    }
};
