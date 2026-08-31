<?php
// database/migrations/2026_08_16_000001_add_manual_fields_to_report_cards_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            // Free-text remarks — referenced by the PDF already but never
            // existed as a real column; the render was silently falling
            // through the empty() check.
            $table->text('teacher_remarks')->nullable()->after('rejection_reason');

            // Attendance, entered manually by the teacher per report card
            // (not pulled from the Attendance model) — absence is derived
            // as total - present at render time rather than stored twice,
            // so the two numbers can't drift out of sync with each other.
            $table->unsignedSmallInteger('attendance_total_days')->nullable()->after('teacher_remarks');
            $table->unsignedSmallInteger('attendance_days_present')->nullable()->after('attendance_total_days');
        });
    }

    public function down(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->dropColumn(['teacher_remarks', 'attendance_total_days', 'attendance_days_present']);
        });
    }
};
