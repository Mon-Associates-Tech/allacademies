<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activity_type'); // view, create, update, delete, download, upload, login, etc.
            $table->string('activity_name'); // More descriptive name: "Quiz Started", "Document Uploaded", etc.
            $table->text('description')->nullable(); // Human-readable description
            $table->string('category')->index(); // authentication, academic, library, communication, payment, system, document, content, settings, assignment, etc.
            $table->nullableMorphs('subject'); // Polymorphic relationship to track what was interacted with (Quiz, Book, Assignment, etc.)
            $table->unsignedBigInteger('reference_id')->nullable(); // Can be used for additional references
            $table->json('metadata')->nullable(); // Additional context: quiz_score, duration, file_size, etc.
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index('user_id');
            $table->index('activity_type');
            // $table->index('category');
            $table->index(['user_id', 'created_at']);
            $table->index(['activity_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
