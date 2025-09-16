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
        Schema::table('books', function (Blueprint $table) {
            $table->renameColumn('single_video_file', 'single_video');
            $table->renameColumn('single_audio_file', 'single_audio');
            $table->renameColumn('chapter_audio_files', 'chapter_audios');
            $table->renameColumn('chapter_video_files', 'chapter_videos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->renameColumn('single_video', 'single_video_file');
            $table->renameColumn('single_audio', 'single_audio_file');
            $table->renameColumn('chapter_audios', 'chapter_audio_files');
            $table->renameColumn('chapter_videos', 'chapter_video_files');
        });
    }
};
