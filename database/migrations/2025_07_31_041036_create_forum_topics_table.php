<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('forum_category_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_announcement')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('academic_level_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->unsignedBigInteger('academic_topic_id')->nullable();
            $table->unsignedBigInteger('study_group_id')->nullable();
            $table->unsignedBigInteger('referenced_book_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('forum_category_id')->references('id')->on('forum_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('academic_level_id')->references('id')->on('academic_levels')->onDelete('set null');
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->onDelete('set null');
            $table->foreign('academic_topic_id')->references('id')->on('academic_topics')->onDelete('set null');
            $table->foreign('study_group_id')->references('id')->on('student_groups')->onDelete('set null');
            $table->foreign('referenced_book_id')->references('id')->on('books')->onDelete('set null');

            $table->index(['forum_category_id', 'last_activity_at']);
            $table->index(['is_pinned', 'is_locked']);
            $table->index(['academic_level_id', 'academic_subject_id']);
            $table->index('views_count');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_topics');
    }
};
