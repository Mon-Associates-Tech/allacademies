<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('moderatable_type');
            $table->unsignedBigInteger('moderatable_id');
            $table->unsignedBigInteger('moderator_id');
            $table->string('action'); // 'pin', 'unpin', 'lock', 'unlock', 'delete', 'restore', 'move', etc.
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable(); // Additional data like old/new values
            $table->timestamps();

            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['moderatable_type', 'moderatable_id']);
            $table->index(['moderator_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_moderation_logs');
    }
};
