<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools');
            }
            if (!Schema::hasColumn('students', 'academic_group_id')) {
                $table->unsignedBigInteger('academic_group_id')->nullable();
                $table->foreign('academic_group_id')->references('id')->on('academic_groups');
            }
            if (!Schema::hasColumn('students', 'academic_level_id')) {
                $table->unsignedBigInteger('academic_level_id')->nullable();
                $table->foreign('academic_level_id')->references('id')->on('academic_levels');
            }
        });

        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools');
            }
        });

        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools');
            }
        });

        Schema::table('academic_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_levels', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools');
            }
        });

        Schema::table('academic_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_groups', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools');
            }
        });

        Schema::table('academic_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_subjects', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable();
                $table->foreign('school_id')->references('id')->on('schools');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
            $table->dropForeign(['academic_group_id']);
            $table->dropColumn('academic_group_id');
            $table->dropForeign(['academic_level_id']);
            $table->dropColumn('academic_level_id');
            $table->dropColumn('school_id');

        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('academic_levels', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('academic_groups', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
