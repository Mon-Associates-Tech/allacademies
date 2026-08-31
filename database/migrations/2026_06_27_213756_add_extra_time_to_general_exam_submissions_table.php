<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            // Total extra minutes granted by admin — accumulated across all extensions
            $table->unsignedSmallInteger('extra_time_minutes')->default(0)->after('time_spent_seconds');
            // Audit trail: who last extended and when
            $table->unsignedBigInteger('extra_time_granted_by')->nullable()->after('extra_time_minutes');
            $table->timestamp('extra_time_granted_at')->nullable()->after('extra_time_granted_by');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropColumn(['extra_time_minutes', 'extra_time_granted_by', 'extra_time_granted_at']);
        });
    }
};