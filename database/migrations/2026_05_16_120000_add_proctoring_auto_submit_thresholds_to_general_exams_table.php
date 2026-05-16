<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->unsignedInteger('auto_submit_high_severity_threshold')
                ->default(2)
                ->after('auto_submit_on_violation');
            $table->unsignedInteger('auto_submit_medium_severity_threshold')
                ->default(5)
                ->after('auto_submit_high_severity_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropColumn([
                'auto_submit_high_severity_threshold',
                'auto_submit_medium_severity_threshold',
            ]);
        });
    }
};
