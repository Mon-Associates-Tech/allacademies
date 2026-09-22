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
        if (! Schema::hasTable('mock_exams')) {
            return;
        }

        /**
         * Add the column first as nullable.
         *
         * This is safe for existing mock_exams rows.
         */
        if (! Schema::hasColumn('mock_exams', 'mock_exam_subscription_id')) {
            Schema::table('mock_exams', function (Blueprint $table) {
                $table->unsignedBigInteger('mock_exam_subscription_id')
                    ->nullable()
                    ->index();
            });
        }

        /**
         * Add the foreign key constraint.
         *
         * This assumes the subscription table has already been renamed to:
         * mock_exam_subscriptions
         */
        if (Schema::hasTable('mock_exam_subscriptions')) {
            Schema::table('mock_exams', function (Blueprint $table) {
                $table->foreign('mock_exam_subscription_id')
                    ->references('id')
                    ->on('mock_exam_subscriptions')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('mock_exams')) {
            return;
        }

        if (! Schema::hasColumn('mock_exams', 'mock_exam_subscription_id')) {
            return;
        }

        /**
         * Drop the foreign key if it exists.
         */
        try {
            Schema::table('mock_exams', function (Blueprint $table) {
                $table->dropForeign(['mock_exam_subscription_id']);
            });
        } catch (\Throwable $e) {
            // The foreign key may not exist or may have a custom name.
        }

        /**
         * Drop the column.
         */
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->dropColumn('mock_exam_subscription_id');
        });
    }
};
