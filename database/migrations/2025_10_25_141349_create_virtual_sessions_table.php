<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_subject_id')->nullable()->constrained()->nullOnDelete();

            // Session Information
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('live');
//            $table->enum('type', ['live', 'recorded'])->default('live');
            $table->string('status')->default('scheduled');
//            $table->enum('status', ['scheduled', 'live', 'ended', 'cancelled'])->default('scheduled');

            // BigBlueButton Integration
            $table->string('meeting_id')->unique()->nullable();
            $table->string('internal_meeting_id')->nullable();
            $table->string('attendee_password')->nullable();
            $table->string('moderator_password')->nullable();
            $table->text('bbb_create_response')->nullable();

            // Session Scheduling
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->integer('duration_minutes')->nullable();

            // Session Settings
            $table->boolean('allow_guest_login')->default(false);
            $table->boolean('auto_record')->default(false);
            $table->boolean('mute_on_start')->default(false);
            $table->boolean('webcams_only_for_moderator')->default(false);
            $table->integer('max_participants')->default(100);
//            $table->enum('guest_policy', ['ALWAYS_ACCEPT', 'ALWAYS_DENY', 'ASK_MODERATOR'])->default('ASK_MODERATOR');
            $table->string('guest_policy')->default('ASK_MODERATOR');

            // Session URLs
            $table->text('join_url')->nullable();
            $table->text('moderator_url')->nullable();

            // Session Metadata
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['school_id', 'status']);
            $table->index(['teacher_id', 'scheduled_start']);
            $table->index('meeting_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_sessions');
    }
};
