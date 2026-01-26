<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_submissions', static function (Blueprint $table) {
            $table->integer('tab_switch_count')->default(0)->after('time_spent_minutes');
            $table->json('violation_logs')->nullable()->after('tab_switch_count');
            $table->boolean('cancelled_due_to_violation')->default(false)->after('violation_logs');
        });
    }

    public function down(): void
    {
        Schema::table('assignment_submissions', static function (Blueprint $table) {
            $table->dropColumn(['tab_switch_count', 'violation_logs', 'cancelled_due_to_violation']);
        });
    }
};
