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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship to attach to any hierarchy level
            $table->string('todoable_type')->nullable(); // AcademicGroup, AcademicLevel, AcademicSubject, AcademicTopic, AcademicSubtopic
            $table->unsignedBigInteger('todoable_id')->nullable();

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Visibility - private by default
            $table->boolean('is_private')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['todoable_type', 'todoable_id']);
            $table->index('user_id');
            $table->index('status');
            $table->index('due_date');
        });

        // Todo sharing table for granular access control
        Schema::create('todo_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained()->onDelete('cascade');

            // Can share with individual user or group
            $table->foreignId('shared_with_user_id')->nullable()->constrained('users')->onDelete('cascade');

            // Polymorphic sharing - can share with academic groups, levels, etc.
            $table->string('shareable_type')->nullable(); // AcademicGroup, AcademicLevel, StudentGroup, etc.
            $table->unsignedBigInteger('shareable_id')->nullable();

            $table->enum('share_type', ['individual', 'academic_group', 'academic_level', 'student_group', 'school_wide'])->default('individual');
            $table->boolean('can_edit')->default(false);

            $table->timestamps();

            $table->index(['shareable_type', 'shareable_id']);
            $table->index('shared_with_user_id');
            $table->unique(['todo_id', 'shared_with_user_id', 'shareable_type', 'shareable_id'], 'todo_share_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_shares');
        Schema::dropIfExists('todos');
    }
};
