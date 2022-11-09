<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('true_or_false_questions', function (Blueprint $table) {
            $table->id();
            $table->json('question');
            $table->boolean('answer');
            $table->integer('score')->default(1);
            $table->string('difficulty_level');
            $table->foreignId('academic_topic_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('true_or_false_questions');
    }
};
