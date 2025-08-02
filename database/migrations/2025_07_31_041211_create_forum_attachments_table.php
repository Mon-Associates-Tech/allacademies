<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type');
            $table->unsignedBigInteger('attachable_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['attachable_type', 'attachable_id']);
            $table->index('user_id');
            $table->index('mime_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_attachments');
    }
};
