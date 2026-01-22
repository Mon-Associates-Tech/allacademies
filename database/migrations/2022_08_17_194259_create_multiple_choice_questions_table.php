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
        Schema::create('multiple_choice_questions', function (Blueprint $table) {
            $table->id();
            $table->json('question');
            $table->json('option_a');
            $table->json('option_b');
            $table->json('option_c');
            $table->json('option_d');
            $table->json('option_e');
            $table->char('answer', 1);
            $table->integer('score')->default(1);
            $table->string('difficulty_level')->default('unspecified');
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
        Schema::dropIfExists('multiple_choice_questions');
    }
};
