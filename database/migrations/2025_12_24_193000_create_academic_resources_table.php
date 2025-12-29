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
        Schema::create('academic_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // pdf, doc, docx, xls, xlsx, image, txt
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship to attach to any hierarchy level
            $table->string('resourceable_type'); // AcademicGroup, AcademicLevel, AcademicSubject, AcademicTopic, AcademicSubtopic
            $table->unsignedBigInteger('resourceable_id');

            $table->boolean('is_public')->default(false);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['resourceable_type', 'resourceable_id']);
            $table->index('user_id');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_resources');
    }
};
