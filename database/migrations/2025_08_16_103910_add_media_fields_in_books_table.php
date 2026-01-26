<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('has_audio')->default(false);
            $table->boolean('has_video')->default(false);
            $table->string('single_audio_file')->nullable();
            $table->string('single_video_file')->nullable();
            $table->json('chapter_audio_files')->nullable();
            $table->json('chapter_video_files')->nullable();
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'has_audio',
                'has_video',
                'single_audio_file',
                'single_video_file',
                'chapter_audio_files',
                'chapter_video_files',
            ]);
        });
    }
};
