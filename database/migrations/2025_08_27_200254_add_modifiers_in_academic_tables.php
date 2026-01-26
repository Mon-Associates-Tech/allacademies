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
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('id');
            $table->unsignedBigInteger('modified_by')->nullable()->after('added_by');

            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('modified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Schema::table('academic_levels', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('id');
            $table->unsignedBigInteger('modified_by')->nullable()->after('added_by');

            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('modified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('id');
            $table->unsignedBigInteger('modified_by')->nullable()->after('added_by');

            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('modified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Schema::table('academic_topics', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('id');
            $table->unsignedBigInteger('modified_by')->nullable()->after('added_by');

            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('modified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Schema::table('academic_subtopics', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('id');
            $table->unsignedBigInteger('modified_by')->nullable()->after('added_by');

            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('modified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table::dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });

        Schema::table('academic_levels', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table::dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });

        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table::dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });

        Schema::table('academic_topics', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table::dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });

        Schema::table('academic_subtopics', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table::dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });
    }
};
