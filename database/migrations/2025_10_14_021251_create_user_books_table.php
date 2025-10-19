<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('content_url')->nullable();
            $table->string('sample_url')->nullable();
            $table->json('table_of_contents')->nullable();
            $table->json('chapter_audios')->nullable();
            $table->json('chapter_videos')->nullable();
            $table->boolean('has_audio')->default(false);
            $table->boolean('has_video')->default(false);
            $table->string('single_audio')->nullable();
            $table->string('single_video')->nullable();
            $table->integer('pages')->nullable();
            $table->string('edition')->nullable();
            $table->string('publisher')->nullable();
            $table->decimal('annual_subscription_fee', 10, 2)->default(0);
            $table->text('subscription_conditions')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });

        Schema::create('user_book_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_book_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shared_to_user_id')->constrained('users')->onDelete('cascade');
            $table->string('shared_to_email');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_book_id', 'shared_to_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_book_shares');
        Schema::dropIfExists('user_books');
    }
};
