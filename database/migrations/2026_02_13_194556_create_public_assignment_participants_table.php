<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_assignment_participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Email verification
            $table->string('verification_token', 64)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('verification_sent_at')->nullable();

            // Result access token - unique token for viewing results without login
            $table->string('result_access_token', 64)->unique();

            // Optional link to existing user/student if they register later
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('set null');

            // Additional metadata
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index('email');
            $table->index('verification_token');
            $table->index('result_access_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_assignment_participants');
    }
};
