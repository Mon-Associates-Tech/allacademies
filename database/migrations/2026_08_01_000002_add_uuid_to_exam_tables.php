<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add uuid to general_exams
        if (!Schema::hasColumn('general_exams', 'uuid')) {
            Schema::table('general_exams', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }

        // Add uuid to general_exam_sections
        if (!Schema::hasColumn('general_exam_sections', 'uuid')) {
            Schema::table('general_exam_sections', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }

        // Add uuid to general_exam_questions
        if (!Schema::hasColumn('general_exam_questions', 'uuid')) {
            Schema::table('general_exam_questions', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }

        // Add uuid to general_exam_submissions
        if (!Schema::hasColumn('general_exam_submissions', 'uuid')) {
            Schema::table('general_exam_submissions', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }

        // Add uuid to exam_proctoring_logs
        if (!Schema::hasColumn('exam_proctoring_logs', 'uuid')) {
            Schema::table('exam_proctoring_logs', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }

        // Add uuid to general_exam_configured_participants
        if (!Schema::hasColumn('general_exam_configured_participants', 'uuid')) {
            Schema::table('general_exam_configured_participants', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }

        // Add uuid to general_exam_participant_groups
        if (!Schema::hasColumn('general_exam_participant_groups', 'uuid')) {
            Schema::table('general_exam_participant_groups', function (Blueprint $table) {
                $table->string('uuid', 26)->unique()->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('general_exam_sections', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('general_exam_questions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('exam_proctoring_logs', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('general_exam_configured_participants', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('general_exam_participant_groups', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
