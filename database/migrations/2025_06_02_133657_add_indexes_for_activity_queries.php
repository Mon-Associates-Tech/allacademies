<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('academic_activities', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('is_group_activity');
            $table->index('start_time');
            $table->index('status');
            $table->index('group_id');

        });

        Schema::table('student_groups', function (Blueprint $table) {
            $table->index('id'); // Should already exist as primary key
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index(['student_group_id']); // or ['student_group_id', 'user_id']
            $table->index('id'); // Should already exist as primary key
        });

        Schema::table('activity_participants', function (Blueprint $table) {
            $table->index(['activity_id']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::table('academic_activities', function (Blueprint $table) {
            $table->dropIndex(['created_by', 'is_group_activity', 'start_time']);
            $table->dropIndex(['is_group_activity', 'group_id', 'start_time']);
            $table->dropIndex(['status', 'start_time']);
            $table->dropIndex(['group_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['student_group_id', 'id']);
        });

        Schema::table('activity_participants', function (Blueprint $table) {
            $table->dropIndex(['activity_id', 'user_id']);
            $table->dropIndex(['user_id']);
        });
    }

};
