<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('book_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // 'video', 'audio', 'playlist', etc.
            $table->string('category')->nullable(); // 'course', 'book', 'podcast', etc.
            $table->json('tags')->nullable();

            // Media content
            $table->string('single_video')->nullable();
            $table->string('single_video_captions')->nullable();
            $table->string('single_video_thumbnail')->nullable();

            $table->string('single_audio')->nullable();
            $table->string('single_audio_captions')->nullable();
            $table->string('single_audio_thumbnail')->nullable();

            $table->json('chapter_videos')->nullable();
            $table->json('chapter_audios')->nullable();

            // Metadata
            $table->integer('duration')->default(0);
            $table->string('author')->nullable();
            $table->string('instructor')->nullable();
            $table->json('metadata')->nullable(); // Additional flexible data

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_media');
    }
};
