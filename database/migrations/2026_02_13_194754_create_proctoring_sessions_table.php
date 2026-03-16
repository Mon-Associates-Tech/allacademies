<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proctoring_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_assignment_submission_id')->constrained()->onDelete('cascade');

            // Session timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            // Session status
            $table->enum('status', ['pending', 'active', 'completed', 'terminated', 'invalid'])->default('pending');

            // Proctoring checks
            $table->boolean('webcam_enabled')->default(false);
            $table->boolean('fullscreen_enabled')->default(false);
            $table->boolean('browser_locked')->default(false);

            // Violation tracking
            $table->integer('tab_switches')->default(0);
            $table->integer('fullscreen_exits')->default(0);
            $table->integer('webcam_disconnects')->default(0);
            $table->integer('copy_paste_attempts')->default(0);
            $table->integer('right_click_attempts')->default(0);

            // Events log - JSON array of {type, timestamp, details}
            $table->json('events')->nullable();

            // Webcam snapshots paths (if enabled)
            $table->json('snapshots')->nullable();

            // Browser/environment info
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->string('ip_address')->nullable();

            // Validation
            $table->boolean('is_valid')->default(true);
            $table->text('invalidation_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index('public_assignment_submission_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proctoring_sessions');
    }
};
