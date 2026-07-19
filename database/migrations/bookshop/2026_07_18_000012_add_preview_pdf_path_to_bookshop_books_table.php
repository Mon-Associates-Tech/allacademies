<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookshop_books', function (Blueprint $table) {
            // cover_image_path already exists from Phase 3 but was never
            // actually wired up to an upload flow until now. This adds the
            // matching field for a sample preview PDF.
            $table->string('preview_pdf_path')->nullable()->after('cover_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('bookshop_books', function (Blueprint $table) {
            $table->dropColumn('preview_pdf_path');
        });
    }
};
