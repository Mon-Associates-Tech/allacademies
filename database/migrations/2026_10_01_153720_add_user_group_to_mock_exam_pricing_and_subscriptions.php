<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exam_pricing_tiers', function (Blueprint $table) {
            $table->string('user_group')->default('individual')
                ->after('name')
                ->comment('individual | school | local_government | organization');
            $table->dropIndex(['subject_count']);
            $table->index(['subject_count', 'user_group', 'is_active']);
        });

        Schema::table('mock_exam_subscriptions', function (Blueprint $table) {
            $table->string('user_group')->default('individual')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('mock_exam_pricing_tiers', function (Blueprint $table) {
            $table->dropIndex(['subject_count', 'user_group', 'is_active']);
            $table->dropColumn('user_group');
            $table->index('subject_count');
        });

        Schema::table('mock_exam_subscriptions', function (Blueprint $table) {
            $table->dropColumn('user_group');
        });
    }
};
