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
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_groups')) {
            Schema::table('academic_groups', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_levels')) {
            Schema::table('academic_levels', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_subjects')) {
            Schema::table('academic_subjects', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_topics')) {
            Schema::table('academic_topics', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('multiple_choice_questions')) {
            Schema::table('multiple_choice_questions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('essay_questions')) {
            Schema::table('essay_questions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('true_or_false_questions')) {
            Schema::table('true_or_false_questions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_groups')) {
            Schema::table('academic_groups', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_levels')) {
            Schema::table('academic_levels', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_subjects')) {
            Schema::table('academic_subjects', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_topics')) {
            Schema::table('academic_topics', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('multiple_choice_questions')) {
            Schema::table('multiple_choice_questions', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('essay_questions')) {
            Schema::table('essay_questions', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        // Check if the table exists before modifying it
        if (Schema::hasTable('true_or_false_questions')) {
            Schema::table('true_or_false_questions', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};