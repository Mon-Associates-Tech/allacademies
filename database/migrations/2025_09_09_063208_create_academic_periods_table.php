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
            $table->foreignId('school_id')->constrained()->onDelete('cascade');

            // Period details
//            $table->string('title')->nullable(); // e.g., 'First Semester', 'Second Semester', 'Third Term', 'Summer Session'
//            $table->string('type')->default('semester'); // semester, term, quarter, trimester
            $table->integer('sequence')->default(1); // 1st, 2nd, 3rd etc.

            // Date range
//            $table->date('start_date');
//            $table->date('end_date');

            // Status and settings
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
//            $table->boolean('is_current')->default(false); // Only one period can be current per school
            $table->json('settings')->nullable(); // Additional period-specific settings

            // Academic year grouping
            //$table->string('academic_year')->nullable(); // e.g., '2024/2025', '2024-2025'
            $table->integer('year_sequence')->nullable(); // For sorting within academic year

            // Optional metadata
            $table->integer('total_weeks')->nullable();
            $table->date('registration_start')->nullable();
            $table->date('registration_end')->nullable();
            $table->date('exam_start')->nullable();
            $table->date('exam_end')->nullable();


            // Indexes
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'academic_year']);
            $table->index(['start_date', 'end_date']);

            // Unique constraint to ensure only one current period per school
            $table->unique(['school_id', 'status'], 'unique_current_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('academic_periods');
    }
};
