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
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->boolean('fullscreen_required')->default(false)->after('auto_advance_sections');
            $table->boolean('copy_paste_disabled')->default(false)->after('fullscreen_required');
            $table->unsignedTinyInteger('tab_switch_limit')->default(0)->after('copy_paste_disabled');
            $table->boolean('auto_submit_on_violation')->default(false)->after('tab_switch_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->dropColumn(['fullscreen_required', 'copy_paste_disabled', 'tab_switch_limit', 'auto_submit_on_violation']);
        });
    }
};
