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
        Schema::create('educational_resources', function (Blueprint $table) {
            $table->id();

            // Basic information
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('tags')->nullable();

            // File properties
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // mime type
            $table->enum('format', ['video', 'pdf', 'image', 'text'])->index();
            $table->unsignedBigInteger('file_size'); // in bytes

            // Academic relationships
            $table->foreignId('academic_subject_id')->constrained('academic_subjects')->onDelete('cascade');

            // School scoping (null means global/public resource)
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');

            // User tracking
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');

            // Status and visibility
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['academic_subject_id', 'is_active']);
            $table->index(['school_id', 'is_active']);
            $table->index(['format', 'is_active']);
        });

        // Pivot table for topics association
        Schema::create('educational_resource_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_resource_id')->constrained('educational_resources')->onDelete('cascade');
            $table->foreignId('academic_topic_id')->constrained('academic_topics')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['educational_resource_id', 'academic_topic_id'], 'resource_topic_unique');
        });

        // Pivot table for subtopics association
        Schema::create('educational_resource_subtopic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_resource_id')->constrained('educational_resources')->onDelete('cascade');
            $table->foreignId('academic_subtopic_id')->constrained('academic_subtopics')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['educational_resource_id', 'academic_subtopic_id'], 'resource_subtopic_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_resource_subtopic');
        Schema::dropIfExists('educational_resource_topic');
        Schema::dropIfExists('educational_resources');
    }
};
