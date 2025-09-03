<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Media folders table
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('path'); // Full path for easy queries
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('media_folders')->onDelete('cascade');
            $table->index(['parent_id', 'name']);
            $table->unique(['parent_id', 'slug']);
        });

        // Media files table
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('disk')->default('public');
            $table->string('mime_type');
            $table->string('extension');
            $table->unsignedBigInteger('size'); // in bytes
            $table->unsignedInteger('width')->nullable(); // for images
            $table->unsignedInteger('height')->nullable(); // for images
            $table->text('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('folder_id')->references('id')->on('media_folders')->onDelete('set null');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->index(['folder_id', 'mime_type']);
            $table->index('uploaded_by');
        });

        // Polymorphic relationship table for attaching media to other models
        Schema::create('media_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('media_file_id');
            $table->morphs('attachable'); // attachable_id, attachable_type
            $table->string('collection')->default('default'); // e.g., 'featured_image', 'gallery', etc.
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('media_file_id')->references('id')->on('media_files')->onDelete('cascade');
            $table->index(['attachable_type', 'attachable_id', 'collection']);
            $table->unique(['media_file_id', 'attachable_type', 'attachable_id', 'collection'], 'unique_media_attachment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_attachments');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('media_folders');
    }
};
