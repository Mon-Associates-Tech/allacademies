<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exam_templates', function (Blueprint $table) {
            // Stores the ordered array of front-page blocks (heading, richtext,
            // image, divider, info_table) that are rendered before the exam body.
            $table->json('front_page_config')->nullable()->after('sections_config');
        });
    }

    public function down(): void
    {
        Schema::table('mock_exam_templates', function (Blueprint $table) {
            $table->dropColumn('front_page_config');
        });
    }
};