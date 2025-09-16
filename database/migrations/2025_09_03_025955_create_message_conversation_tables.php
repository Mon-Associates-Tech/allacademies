<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('custom'); // 'academic_level', 'academic_group', 'custom', 'direct'
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // For academic-based chats
            $table->foreignId('academic_level_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('academic_group_id')->nullable()->constrained()->cascadeOnDelete();

            $table->boolean('is_private')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Additional settings like max_members, etc.

            $table->timestamps();

            $table->index(['school_id', 'type']);
            $table->index(['academic_level_id']);
            $table->index(['academic_group_id']);
        });

        Schema::create('chat_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['member', 'admin', 'moderator'])->default('member');
            $table->boolean('can_add_members')->default(false);
            $table->boolean('can_remove_members')->default(false);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['chat_group_id', 'user_id']);
            $table->index(['user_id', 'is_active']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->enum('message_type', ['text', 'file', 'image', 'system'])->default('text');

            // For replies
            $table->foreignId('reply_to_message_id')->nullable()->constrained('chat_messages')->cascadeOnDelete();

            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();

            $table->timestamps();

            $table->index(['chat_group_id', 'created_at']);
            $table->index(['user_id']);
        });

        Schema::create('chat_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->timestamps();

            $table->index(['chat_message_id']);
        });

        Schema::create('chat_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['chat_message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_groups');
        Schema::dropIfExists('chat_group_members');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_message_attachments');
        Schema::dropIfExists('chat_message_reads');
    }
};
