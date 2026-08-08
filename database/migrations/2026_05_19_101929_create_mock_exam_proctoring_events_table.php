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
        Schema::create('mock_exam_proctoring_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_submission_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', ['tab_switch', 'fullscreen_exit', 'paste_attempt', 'focus_loss', 'auto_submitted']);
            $table->timestamp('occurred_at');
            $table->json('details')->nullable();
            $table->timestamps();
            
            $table->index(['mock_exam_submission_id', 'event_type'], 'proctor_events_submission_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_exam_proctoring_events');
    }
};
