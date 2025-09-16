<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Migrate table of contents
        DB::table('books')->orderBy('id', 'desc')->chunk(100, function ($books) {
            foreach ($books as $book) {
                if ($book->table_of_contents) {
                    DB::table('book_table_of_contents')->insert([
                        'book_id' => $book->id,
                        'contents' => $book->table_of_contents,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        // Migrate media
        DB::table('books')->orderBy('id', 'desc')->chunk(100, function ($books) {
            foreach ($books as $book) {
                $mediaData = [
                    'book_id' => $book->id,
                    'single_audio' => $book->single_audio_file,
                    'single_video' => $book->single_video_file,
                    'chapter_audios' => $book->chapter_audio_files,
                    'chapter_videos' => $book->chapter_video_files,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table('book_media')->insert($mediaData);
            }
        });
    }

    public function down()
    {
        // Reverse migration if needed
        DB::table('book_table_of_contents')->orderBy('id', 'desc')->chunk(100, function ($contents) {
            foreach ($contents as $content) {
                DB::table('books')
                    ->where('id', $content->book_id)
                    ->update(['table_of_contents' => $content->content]);
            }
        });

        DB::table('book_media')->orderBy('id', 'desc')->chunk(100, function ($mediaItems) {
            foreach ($mediaItems as $media) {
                DB::table('books')
                    ->where('id', $media->book_id)
                    ->update([
                        'single_audio_file' => $media->single_audio,
                        'single_video_file' => $media->single_video,
                        'chapter_audio_files' => $media->chapter_audios,
                        'chapter_video_files' => $media->chapter_videos,
                    ]);
            }
        });
    }
};
