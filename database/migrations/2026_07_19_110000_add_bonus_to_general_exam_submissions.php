<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->decimal('bonus_points', 5, 2)->default(0)->after('percentage');
            $table->string('bonus_reason')->nullable()->after('bonus_points');
            $table->unsignedBigInteger('bonus_granted_by')->nullable()->after('bonus_reason');
            $table->timestamp('bonus_granted_at')->nullable()->after('bonus_granted_by');
        });
    }

    public function down(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropColumn(['bonus_points', 'bonus_reason', 'bonus_granted_by', 'bonus_granted_at']);
        });
    }
};
