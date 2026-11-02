<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_exam_configured_participants', 'center_id')) {
            Schema::table('general_exam_configured_participants', function (Blueprint $table) {
                $table->unsignedBigInteger('center_id')->nullable()->after('general_exam_id');
                $table->foreign('center_id')->references('id')->on('general_exam_centers')->nullOnDelete();
                $table->index('center_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('general_exam_configured_participants', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropIndex(['center_id']);
            $table->dropColumn('center_id');
        });
    }
};
