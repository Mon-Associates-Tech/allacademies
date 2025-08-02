<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscribable_type');
            $table->unsignedBigInteger('subscribable_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['subscribable_type', 'subscribable_id']);
            $table->unique(['subscribable_type', 'subscribable_id', 'user_id'], 'unique_subscription');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_subscriptions');
    }
};
