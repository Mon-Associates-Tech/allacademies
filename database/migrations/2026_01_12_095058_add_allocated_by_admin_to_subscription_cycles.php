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
        Schema::table('subscription_cycles', function (Blueprint $table) {
            $table->boolean('allocated_by_admin')->default(false)->after('is_trial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_cycles', function (Blueprint $table) {
            $table->dropColumn('allocated_by_admin');
        });
    }
};
