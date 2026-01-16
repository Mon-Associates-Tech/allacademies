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
        if (! Schema::hasTable('quiz_sessions')) {
            Schema::create('quiz_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('book_id')->nullable();
                $table->unsignedBigInteger('chapter_id')->nullable();
                $table->integer('page_start')->nullable();
                $table->integer('page_end')->nullable();
                $table->string('question_type');
                $table->integer('question_count');
                $table->string('difficulty');
                $table->json('questions')->nullable();
                $table->json('answers')->nullable();
                $table->json('results')->nullable();
                $table->json('context')->nullable();
                $table->integer('time_taken')->nullable();
                $table->string('status')->default('active'); // active, completed
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('book_id')->references('id')->on('books')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
    }
};
