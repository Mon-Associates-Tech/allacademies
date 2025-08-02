<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->unsignedBigInteger('forum_topic_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_answer')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('dislikes_count')->default(0);
            $table->timestamp('edited_at')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->text('edit_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('forum_topic_id')->references('id')->on('forum_topics')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('forum_posts')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['forum_topic_id', 'created_at']);
            $table->index(['parent_id']);
            $table->index('is_approved');
            $table->index(['likes_count', 'dislikes_count']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_posts');
    }
};
