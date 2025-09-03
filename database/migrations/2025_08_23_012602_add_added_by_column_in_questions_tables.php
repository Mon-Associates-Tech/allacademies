<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('true_or_false_questions', function (Blueprint $table) {
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
        Schema::table('multiple_choice_questions', function (Blueprint $table) {
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

        Schema::table('essay_questions', function (Blueprint $table) {
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
        Schema::table('true_or_false_questions', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });
        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });
        Schema::table('essay_questions', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropForeign(['modified_by']);
            $table->dropColumn(['added_by', 'modified_by']);
        });
    }
};
