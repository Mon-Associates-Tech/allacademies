<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookshop_staff', function (Blueprint $table) {
            // A branch admin without a branch_id is treated as unassigned
            // (not superadmin) — routes/services should block dashboard
            // access until a superadmin assigns them one.
            $table->foreign('branch_id')
                ->references('id')->on('bookshop_branches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookshop_staff', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
    }
};
