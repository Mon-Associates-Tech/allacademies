<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_card_configurations', function (Blueprint $table): void {
            if (! Schema::hasColumn('report_card_configurations', 'min_subjects')) {
                $table->unsignedInteger('min_subjects')->nullable()->after('preparation_mode');
            }
            if (! Schema::hasColumn('report_card_configurations', 'max_subjects')) {
                $table->unsignedInteger('max_subjects')->nullable()->after('min_subjects');
            }
        });
    }

    public function down(): void
    {
        Schema::table('report_card_configurations', function (Blueprint $table): void {
            if (Schema::hasColumn('report_card_configurations', 'max_subjects')) {
                $table->dropColumn('max_subjects');
            }
            if (Schema::hasColumn('report_card_configurations', 'min_subjects')) {
                $table->dropColumn('min_subjects');
            }
        });
    }
};
