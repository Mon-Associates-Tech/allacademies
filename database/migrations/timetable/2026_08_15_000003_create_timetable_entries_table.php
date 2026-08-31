<?php
// database/migrations/timetable/2026_08_15_000003_create_timetable_entries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained()->cascadeOnDelete(); // implies the academic year via AcademicPeriod->academicYear()
            $table->foreignId('academic_level_id')->constrained()->cascadeOnDelete(); // the "class"
            $table->foreignId('academic_subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained()->cascadeOnDelete();

            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);

            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // A class can't have two subjects in the same slot, same day, same period.
            $table->unique(
                ['academic_level_id', 'day_of_week', 'time_slot_id', 'academic_period_id'],
                'timetable_class_slot_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
