<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            // Rotated on every authenticate() call.  HeartbeatController compares
            // the session-stored token against this column; a mismatch means a
            // second device has taken over the session and the old device is kicked.
            $table->string('device_token', 64)->nullable()->after('extra_time_granted_at');

            $table->index('device_token');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropIndex(['device_token']);
            $table->dropColumn('device_token');
        });
    }
};