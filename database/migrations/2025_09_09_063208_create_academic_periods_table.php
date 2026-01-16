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
        Schema::table('academic_periods', function (Blueprint $table) {
            // Check if school_id column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'school_id')) {
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
            }

            // Check if sequence column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'sequence')) {
                $table->integer('sequence')->default(1);
            }

            // Check if status column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'status')) {
                $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            }

            // Check if settings column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'settings')) {
                $table->json('settings')->nullable();
            }

            // Check if year_sequence column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'year_sequence')) {
                $table->integer('year_sequence')->nullable();
            }

            // Check if total_weeks column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'total_weeks')) {
                $table->integer('total_weeks')->nullable();
            }

            // Check if registration_start column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'registration_start')) {
                $table->date('registration_start')->nullable();
            }

            // Check if registration_end column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'registration_end')) {
                $table->date('registration_end')->nullable();
            }

            // Check if exam_start column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'exam_start')) {
                $table->date('exam_start')->nullable();
            }

            // Check if exam_end column exists before adding it
            if (! Schema::hasColumn('academic_periods', 'exam_end')) {
                $table->date('exam_end')->nullable();
            }

            // Check if indexes exist before adding them
            if (! Schema::hasIndex('academic_periods', ['school_id', 'status'])) {
                $table->index(['school_id', 'status']);
            }

            if (! Schema::hasIndex('academic_periods', ['school_id', 'academic_year'])) {
                $table->index(['school_id', 'academic_year']);
            }

            if (! Schema::hasIndex('academic_periods', ['start_date', 'end_date'])) {
                $table->index(['start_date', 'end_date']);
            }

            // Check if unique constraint exists before adding it
            if (! Schema::hasIndex('academic_periods', 'unique_current_period')) {
                $table->unique(['school_id', 'status'], 'unique_current_period');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('academic_periods');
    }
};
