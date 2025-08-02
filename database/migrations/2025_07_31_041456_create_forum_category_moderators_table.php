<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_category_moderators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_category_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->foreign('forum_category_id')->references('id')->on('forum_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['forum_category_id', 'user_id']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_category_moderators');
    }
};
