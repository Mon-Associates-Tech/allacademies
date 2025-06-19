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
        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->on('academic_activities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->nullable(); // 'invited', 'confirmed', 'attended', 'absent', etc.
            $table->decimal('score', 8, 2)->nullable(); // Store participant's score if applicable
            $table->string('attendance')->nullable(); // 'present', 'absent', 'late', etc.
            $table->timestamps();

            // Ensure a user can only be added once to an activity
            $table->unique(['activity_id', 'user_id']);

            // Add indexes for fields frequently used in queries
            $table->index('status');
            $table->index('score');
            $table->index('attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_participants');
    }
};
