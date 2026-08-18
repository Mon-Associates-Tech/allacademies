<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_participant_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });

        Schema::create('general_exam_participant_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('general_exam_participant_groups')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('unique_code')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['group_id', 'email']);
            $table->index(['group_id', 'name']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_participant_group_members');
        Schema::dropIfExists('general_exam_participant_groups');
    }
};
