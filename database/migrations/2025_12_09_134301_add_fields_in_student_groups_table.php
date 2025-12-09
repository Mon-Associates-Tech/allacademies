<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_groups', static function (Blueprint $table) {
            // Add school_id as mandatory
            $table->unsignedBigInteger('school_id')->after('id');

            // Add optional academic relationships
            $table->unsignedBigInteger('academic_group_id')->nullable()->after('school_id');
            $table->unsignedBigInteger('academic_level_id')->nullable()->after('academic_group_id');
            $table->unsignedBigInteger('academic_subject_id')->nullable()->after('academic_level_id');

            // Make teacher_id nullable
            $table->unsignedBigInteger('teacher_id')->nullable()->change();

            // Add foreign keys
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('academic_group_id')->references('id')->on('academic_groups')->onDelete('set null');
            $table->foreign('academic_level_id')->references('id')->on('academic_levels')->onDelete('set null');
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->onDelete('set null');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

            // Add indexes for better performance
            $table->index(['school_id', 'is_active']);
            $table->index('academic_group_id');
            $table->index('academic_level_id');
            $table->index('academic_subject_id');
        });
    }

    public function down(): void
    {
        Schema::table('student_groups', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['academic_group_id']);
            $table->dropForeign(['academic_level_id']);
            $table->dropForeign(['academic_subject_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['creator_id']);

            $table->dropIndex(['school_id', 'is_active']);
            $table->dropIndex(['academic_group_id']);
            $table->dropIndex(['academic_level_id']);
            $table->dropIndex(['academic_subject_id']);

            $table->dropColumn([
                'school_id',
                'academic_group_id',
                'academic_level_id',
                'academic_subject_id'
            ]);

            // Revert teacher_id to not nullable (if it was before)
            $table->unsignedBigInteger('teacher_id')->nullable(false)->change();
        });
    }
};
