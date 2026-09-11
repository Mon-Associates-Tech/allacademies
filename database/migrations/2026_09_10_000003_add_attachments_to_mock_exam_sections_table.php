<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->boolean('insert_blank_page')->default(false)->after('subtopic_ids');
            $table->string('attachment_original_name')->nullable()->after('insert_blank_page');
            $table->string('attachment_extension', 10)->nullable()->after('attachment_original_name');
            $table->longText('attachment_text')->nullable()->after('attachment_extension');
            $table->string('attachment_image_path')->nullable()->after('attachment_text');
            $table->json('attachment_pdf_images')->nullable()->after('attachment_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->dropColumn([
                'insert_blank_page',
                'attachment_original_name',
                'attachment_extension',
                'attachment_text',
                'attachment_image_path',
                'attachment_pdf_images',
            ]);
        });
    }
};