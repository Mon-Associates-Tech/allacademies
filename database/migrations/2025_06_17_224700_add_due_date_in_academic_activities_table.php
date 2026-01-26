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
        Schema::table('academic_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_activities', 'due_date')) {
                $table->timestamp('due_date')
                    ->nullable()
                    ->after('end_time')
                    ->comment('The due date for the academic activity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_activities', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
