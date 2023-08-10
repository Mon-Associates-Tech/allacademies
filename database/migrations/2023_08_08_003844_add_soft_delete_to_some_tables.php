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
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('academic_levels', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('academic_topics', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('essay_questions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('true_or_false_questions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('academic_levels', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('academic_topics', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('essay_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('true_or_false_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
