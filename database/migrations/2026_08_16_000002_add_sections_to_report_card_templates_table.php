<?php
// database/migrations/2026_08_16_000002_add_sections_to_report_card_templates_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_card_templates', function (Blueprint $table) {
            // Single structured JSON blob for the whole builder — header,
            // student_info, grades_table, attendance, remarks, signatures,
            // footer. Supersedes the old unstructured header_config/
            // footer_config columns, which are left in place (unused) rather
            // than dropped, in case anything still references them.
            $table->json('sections')->nullable()->after('custom_columns');
        });
    }

    public function down(): void
    {
        Schema::table('report_card_templates', function (Blueprint $table) {
            $table->dropColumn('sections');
        });
    }
};
