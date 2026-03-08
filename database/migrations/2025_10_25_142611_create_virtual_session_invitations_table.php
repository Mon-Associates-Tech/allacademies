<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_session_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            // Guest Information (if not registered user)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            // Invitation Details
            $table->string('token')->unique();
            $table->string('status')->default('pending');
            //            $table->enum('status', ['pending', 'accepted', 'declined', 'expired'])->default('pending');
            $table->text('message')->nullable();

            // Timestamps
            $table->dateTime('invited_at');
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('declined_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('last_reminder_at')->nullable();

            // Sender
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['virtual_session_id', 'status']);
            $table->index('token');
            $table->index('guest_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_session_invitations');
    }
};
