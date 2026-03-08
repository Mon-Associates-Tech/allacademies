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
        Schema::create('calendar_event_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('remind_at')->comment('When the reminder should be sent');
            $table->integer('minutes_before')->default(15)->comment('Minutes before event to send reminder');
            $table->json('channels')->comment('Notification channels: email, database, sms');
            $table->boolean('is_sent')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->string('status')->default('pending')->comment('pending, sent, failed, cancelled');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['remind_at', 'is_sent']);
            $table->index(['calendar_event_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_event_reminders');
    }
};
