<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calendar_event_shares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calendar_event_id');
            $table->unsignedBigInteger('shared_with_user_id')->nullable();
            $table->string('guest_email')->nullable();
            $table->enum('share_type', ['individual', 'academic_group', 'academic_level', 'student_group', 'school_wide']);
            $table->string('shareable_type')->nullable();
            $table->unsignedBigInteger('shareable_id')->nullable();
            $table->boolean('can_edit')->default(false);
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->foreign('calendar_event_id')->references('id')->on('calendar_events')->onDelete('cascade');
            $table->foreign('shared_with_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['calendar_event_id']);
            $table->index([ 'shared_with_user_id']);
            $table->index(['shareable_type', 'shareable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('calendar_event_shares');
    }
};
