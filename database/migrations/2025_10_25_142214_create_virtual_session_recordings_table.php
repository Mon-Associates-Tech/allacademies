<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_session_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();

            // Recording Information
            $table->string('recording_id')->unique();
            $table->string('internal_recording_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();

            // Recording Details
            $table->string('type')->default('bbb');
//            $table->enum('type', ['bbb', 'uploaded'])->default('bbb');
            $table->string('status')->default('processing');
//            $table->enum('status', ['processing', 'published', 'unpublished', 'deleted'])->default('processing');
            $table->string('format')->nullable(); // presentation, video, etc.

            // BBB Recording Data
            $table->text('playback_url')->nullable();
            $table->text('download_url')->nullable();
            $table->bigInteger('size_bytes')->nullable();
            $table->integer('duration_seconds')->nullable();

            // Local Storage (if downloaded)
            $table->string('storage_disk')->nullable();
            $table->string('storage_path')->nullable();
            $table->string('thumbnail_path')->nullable();

            // Timestamps
            $table->dateTime('recorded_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('downloaded_at')->nullable();
            $table->dateTime('expires_at')->nullable();

            // Access Control
            $table->boolean('is_public')->default(false);
            $table->boolean('allow_download')->default(false);
            $table->json('access_settings')->nullable();

            // Metadata
            $table->json('bbb_metadata')->nullable();
            $table->json('playback_formats')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['virtual_session_id', 'status']);
            $table->index(['school_id', 'status']);
            $table->index('recording_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_session_recordings');
    }
};
