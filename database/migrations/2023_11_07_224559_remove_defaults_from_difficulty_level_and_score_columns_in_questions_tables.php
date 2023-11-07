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
        Schema::table('essay_questions', function (Blueprint $table) {
            $table->string('difficulty_level')->nullable(false)->change();
            $table->integer('score')->nullable(false)->change();
        });

        Schema::table('true_or_false_questions', function (Blueprint $table) {
            $table->string('difficulty_level')->nullable(false)->change();
            $table->integer('score')->nullable(false)->change();
        });

        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->string('difficulty_level')->nullable(false)->change();
            $table->integer('score')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('essay_questions', function (Blueprint $table) {
            $table->string('difficulty_level')->default('unspecified')->change();
            $table->integer('score')->default(15)->change();
        });

        Schema::table('true_or_false_questions', function (Blueprint $table) {
            $table->string('difficulty_level')->default('unspecified')->change();
            $table->integer('score')->default(1)->change();
        });

        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->string('difficulty_level')->default('unspecified')->change();
            $table->integer('score')->default(1)->change();
        });
    }
};
