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
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Add 'cancelled' and 'completed' status options to the enum
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'graded', 'completed', 'cancelled'])
                ->default('not_started')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Revert to original enum values (note: this will fail if any rows have 'cancelled' or 'completed' status)
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'graded'])
                ->default('not_started')
                ->change();
        });
    }
};
