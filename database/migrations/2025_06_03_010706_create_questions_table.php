<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionable_id');
            $table->string('questionable_type');
            $table->string('difficulty_level')->nullable(); // easy, medium, hard
            $table->integer('points')->default(1);
            $table->unsignedBigInteger('subtopic_id')->nullable();
            $table->unsignedBigInteger('topic_id')->nullable();
            $table->unsignedBigInteger('user_id'); // creator
            $table->timestamps();

            $table->foreign('subtopic_id')->references('id')->on('academic_subtopics')->onDelete('set null');
            $table->foreign('topic_id')->references('id')->on('academic_topics')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
