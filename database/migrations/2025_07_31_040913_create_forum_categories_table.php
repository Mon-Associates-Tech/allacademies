<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('violet');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_private')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('academic_level_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->unsignedBigInteger('book_category_id')->nullable();
            $table->string('required_role')->nullable();
            $table->json('moderator_ids')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('forum_categories')->onDelete('cascade');
            $table->foreign('academic_level_id')->references('id')->on('academic_levels')->onDelete('set null');
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->onDelete('set null');
            $table->foreign('book_category_id')->references('id')->on('book_categories')->onDelete('set null');

            $table->index(['is_active', 'sort_order']);
            $table->index(['academic_level_id', 'academic_subject_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_categories');
    }
};
