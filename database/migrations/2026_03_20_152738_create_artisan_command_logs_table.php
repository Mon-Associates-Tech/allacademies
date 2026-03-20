<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artisan_command_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('command');
            $table->json('arguments')->nullable();
            $table->text('output')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->index(['user_id', 'executed_at']);
            $table->index('command');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisan_command_logs');
    }
};
