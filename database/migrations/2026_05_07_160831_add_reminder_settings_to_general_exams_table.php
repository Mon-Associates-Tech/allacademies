<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->boolean('send_reminders')->default(false)->after('access_code');
            $table->timestamp('reminder_datetime')->nullable()->after('send_reminders');
            $table->boolean('reminder_sent')->default(false)->after('reminder_datetime');
            $table->timestamp('reminder_sent_at')->nullable()->after('reminder_sent');
        });
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropColumn(['send_reminders', 'reminder_datetime', 'reminder_sent', 'reminder_sent_at']);
        });
    }
};
