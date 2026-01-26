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
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'percentage_score')) {
                $table->float('percentage_score')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'max_score')) {
                $table->float('max_score')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'total_score')) {
                $table->float('total_score')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('percentage_score');
            $table->dropColumn('max_score');
            $table->dropColumn('total_score');
        });
    }
};
