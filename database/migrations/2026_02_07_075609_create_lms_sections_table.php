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
        if (! Schema::hasTable('lms_sections')) {
            Schema::create('lms_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chapter_id')->constrained('lms_chapters')->cascadeOnDelete();
                $table->foreignId('parent_section_id')->nullable()->constrained('lms_sections')->nullOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();

                $table->index('chapter_id');
                $table->index('parent_section_id');
                $table->index('order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_sections');
    }
};
