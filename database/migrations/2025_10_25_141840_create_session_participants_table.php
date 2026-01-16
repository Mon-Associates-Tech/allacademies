<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_session_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Participant Information
            $table->string('role')->default('attendee');
            //            $table->enum('role', ['moderator', 'attendee', 'guest'])->default('attendee');
            $table->string('status')->default('invited');
            //            $table->enum('status', ['invited', 'joined', 'left', 'declined'])->default('invited');

            // Join Information
            $table->string('full_name')->nullable();
            $table->string('bbb_user_id')->nullable();
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->integer('duration_seconds')->nullable();

            // Invitation
            $table->dateTime('invited_at')->nullable();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('invitation_message')->nullable();

            // Engagement Metrics
            $table->boolean('has_joined')->default(false);
            $table->integer('join_count')->default(0);
            $table->json('join_history')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['virtual_session_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->unique(['virtual_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_session_participants');
    }
};
