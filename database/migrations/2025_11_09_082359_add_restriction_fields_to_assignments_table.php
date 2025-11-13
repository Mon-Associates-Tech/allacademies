<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', static function (Blueprint $table) {
            $table->boolean('restrict_navigation')->default(false)->after('is_randomized');
            $table->integer('max_tab_switches')->nullable()->after('restrict_navigation');
            $table->boolean('auto_submit_on_violation')->default(true)->after('max_tab_switches');
        });
    }

    public function down(): void
    {
        Schema::table('assignments', static function (Blueprint $table) {
            $table->dropColumn(['restrict_navigation', 'max_tab_switches', 'auto_submit_on_violation']);
        });
    }
};
