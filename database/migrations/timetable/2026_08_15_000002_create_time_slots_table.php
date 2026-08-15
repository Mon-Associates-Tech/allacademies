<?php
// database/migrations/timetable/2026_08_15_000002_create_time_slots_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('label'); // e.g. "Period 1"
            $table->time('starts_at');
            $table->time('ends_at');
            $table->unsignedInteger('order')->default(0); // display ordering
            $table->boolean('is_break')->default(false); // e.g. lunch/break periods, excluded from teaching conflicts
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
