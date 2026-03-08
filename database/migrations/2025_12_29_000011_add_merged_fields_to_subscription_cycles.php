<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_cycles', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_cycles', 'merged_with_group_id')) {
                $table->string('merged_with_group_id')->nullable();
            }
            if (! Schema::hasColumn('subscription_cycles', 'is_merged')) {
                $table->boolean('is_merged')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_cycles', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_cycles', 'merged_with_group_id')) {
                $table->dropColumn('merged_with_group_id');
            }
            if (Schema::hasColumn('subscription_cycles', 'is_merged')) {
                $table->dropColumn('is_merged');
            }
        });
    }
};
