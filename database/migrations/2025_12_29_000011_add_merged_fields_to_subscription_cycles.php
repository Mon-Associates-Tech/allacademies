<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_cycles', function (Blueprint $table) {
            $table->string('merged_with_group_id')->nullable()->after('subscription_group_id');
            $table->boolean('is_merged')->default(false)->after('is_topup');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_cycles', function (Blueprint $table) {
            $table->dropColumn(['merged_with_group_id', 'is_merged']);
        });
    }
};
