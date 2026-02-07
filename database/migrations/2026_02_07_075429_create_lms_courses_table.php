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
        if (! Schema::hasTable('lms_courses')) {
            Schema::create('lms_courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->text('objectives')->nullable();
                $table->string('thumbnail')->nullable();
                $table->string('difficulty_level')->default('beginner'); // beginner, intermediate, advanced
                $table->string('audience')->default('public'); // public, school_only
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_free')->default(true);
                $table->string('status')->default('draft'); // draft, published, unpublished, archived
                $table->integer('estimated_duration_minutes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'audience']);
                $table->index('school_id');
                $table->index('created_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_courses');
    }
};
