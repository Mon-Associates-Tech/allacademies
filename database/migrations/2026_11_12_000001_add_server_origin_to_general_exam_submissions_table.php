<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add server_origin column to track which server (local or central)
     * owns an exam attempt. Used by the load balancer for sticky sessions
     * and cross-server submission reconciliation.
     */
    public function up(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->string('server_origin', 10)
                ->nullable()
                ->after('user_agent')
                ->comment('Which server owns this attempt: "local" or "central"');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropColumn('server_origin');
        });
    }
};
