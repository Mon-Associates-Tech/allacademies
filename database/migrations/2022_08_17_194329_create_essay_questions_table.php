<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essay_questions', function (Blueprint $table) {
            $table->id();
            $table->json('question');
            $table->json('answer');
            $table->integer('score');
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
        Schema::dropIfExists('essay_questions');
    }
};
