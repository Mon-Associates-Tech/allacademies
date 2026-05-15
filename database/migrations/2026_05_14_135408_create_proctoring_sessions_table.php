<?php
/**
 * Migration for Proctoring Sessions (Polymorphic)
 *
 * Creates a flexible proctoring sessions table that supports any
 * examinable model via Laravel's polymorphic relationships.
 * Tracks user, school, session token, violation counts, and lifecycle status.
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_proctoring_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic relationship to any examinable model
            $table->string('proctorable_type');
            $table->unsignedBigInteger('proctorable_id');
            $table->index(['proctorable_type', 'proctorable_id']);

            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('session_token')->unique();
            $table->integer('violation_count')->default(0);
            $table->enum('status', ['active', 'warning', 'suspended', 'completed', 'auto_submitted'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_proctoring_sessions');
    }
};
