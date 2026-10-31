<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('exam_proctoring_logs', 'center_id')) {
            Schema::table('exam_proctoring_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('center_id')->nullable()->after('uuid');
                $table->timestamp('synced_at')->nullable()->after('center_id');
                $table->index('center_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('exam_proctoring_logs', function (Blueprint $table) {
            $table->dropIndex(['exam_proctoring_logs_center_id']);
            $table->dropColumn(['center_id', 'synced_at']);
        });
    }
};
