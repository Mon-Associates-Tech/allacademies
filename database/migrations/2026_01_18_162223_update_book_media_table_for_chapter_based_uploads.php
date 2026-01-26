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
        Schema::table('book_media', function (Blueprint $table) {
            // Check and modify chapter_videos column if it exists
            if (Schema::hasColumn('book_media', 'chapter_videos')) {
                $table->json('chapter_videos')->nullable()->change();
            }

            // Check and modify chapter_audios column if it exists
            if (Schema::hasColumn('book_media', 'chapter_audios')) {
                $table->json('chapter_audios')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes needed for rollback
    }
};
