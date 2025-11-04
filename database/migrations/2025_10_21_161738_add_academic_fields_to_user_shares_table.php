<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_book_shares', function (Blueprint $table) {
            // Add support for group/level sharing
            $table->unsignedBigInteger('academic_group_id')->nullable()->after('shared_to_user_id');
            $table->unsignedBigInteger('academic_level_id')->nullable()->after('academic_group_id');
            $table->unsignedBigInteger('student_group_id')->nullable()->after('academic_level_id');

            // Add share type to differentiate between individual and group shares
            $table->string('share_type')
                ->default('individual')
                ->after('status');

            // Add expires_at for time-limited sharing
            $table->timestamp('expires_at')->nullable()->after('accepted_at');

            // Add notes for share context
            $table->text('notes')->nullable()->after('expires_at');

            // Add foreign keys
            $table->foreign('academic_group_id')
                ->references('id')
                ->on('academic_groups')
                ->onDelete('cascade');

            $table->foreign('academic_level_id')
                ->references('id')
                ->on('academic_levels')
                ->onDelete('cascade');

            $table->foreign('student_group_id')
                ->references('id')
                ->on('student_groups')
                ->onDelete('cascade');

            // Add composite indexes for better query performance
            $table->index(['user_book_id', 'share_type']);
            $table->index(['academic_group_id', 'status']);
            $table->index(['academic_level_id', 'status']);
            $table->index(['student_group_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('user_book_shares', function (Blueprint $table) {
            $table->dropForeign(['academic_group_id']);
            $table->dropForeign(['academic_level_id']);
            $table->dropForeign(['student_group_id']);

            $table->dropColumn([
                'academic_group_id',
                'academic_level_id',
                'student_group_id',
                'share_type',
                'expires_at',
                'notes'
            ]);
        });
    }
};
