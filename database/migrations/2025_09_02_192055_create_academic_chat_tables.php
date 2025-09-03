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
        Schema::create('academic_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->json('parameters')->nullable(); // Store educational parameters
            $table->json('messages')->nullable(); // Store conversation history
            $table->json('analytics')->nullable(); // Store usage analytics
            $table->timestamp('last_activity');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('academic_chat_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('subject')->nullable();
            $table->json('topics')->nullable();
            $table->string('academic_level')->nullable();
            $table->integer('age')->nullable();
            $table->string('learning_style')->nullable();
            $table->integer('message_count')->default(0);
            $table->integer('total_tokens_used')->default(0);
            $table->decimal('average_response_time', 8, 2)->nullable();
            $table->json('learning_outcomes')->nullable(); // Track learning progress
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_chat_analytics');
        Schema::dropIfExists('academic_chat_sessions');
    }
};
