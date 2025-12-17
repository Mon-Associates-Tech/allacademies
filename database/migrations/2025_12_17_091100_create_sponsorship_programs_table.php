<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('project'); // project, cause, scholarship, emergency
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->text('affected_individuals')->nullable();
            $table->decimal('amount_goal', 12, 2)->default(0);
            $table->decimal('amount_raised', 12, 2)->default(0);
            $table->date('deadline')->nullable();
            $table->string('status')->default('draft'); // draft, pending_verification, active, completed, cancelled
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_programs');
    }
};
