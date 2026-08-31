<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('sender_id')
                ->constrained('message_templates')->nullOnDelete();
            // ['email', 'sms', 'in_app']
            $table->json('channels')->nullable()->after('is_urgent');
            $table->string('context_type')->nullable()->after('channels'); // e.g. 'fee_reminder'
            $table->json('context_data')->nullable()->after('context_type'); // resolved data snapshot
        });

        Schema::table('message_recipients', function (Blueprint $table) {
            $table->boolean('sms_sent')->default(false)->after('email_sent');
            $table->timestamp('sms_sent_at')->nullable()->after('sms_sent');
            $table->boolean('in_app_sent')->default(false)->after('sms_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn(['template_id', 'channels', 'context_type', 'context_data']);
        });

        Schema::table('message_recipients', function (Blueprint $table) {
            $table->dropColumn(['sms_sent', 'sms_sent_at', 'in_app_sent']);
        });
    }
};
