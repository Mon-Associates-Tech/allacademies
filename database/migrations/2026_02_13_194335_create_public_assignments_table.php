<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('access_code', 12)->unique();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');

            // Assignment type and configuration
            $table->enum('type', ['quiz', 'examination', 'practice'])->default('quiz');
            $table->integer('duration_in_minutes')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_randomized')->default(false);
            $table->text('instructions')->nullable();
            $table->integer('total_marks')->default(0);

            // Result visibility settings
            $table->enum('result_visibility', ['immediate', 'after_due_date', 'manual_release'])->default('immediate');
            $table->boolean('results_released')->default(false);
            $table->timestamp('results_released_at')->nullable();
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('show_score_breakdown')->default(true);

            // Proctoring settings
            $table->boolean('proctoring_enabled')->default(true);
            $table->boolean('restrict_navigation')->default(true);
            $table->integer('max_tab_switches')->default(3);
            $table->boolean('auto_submit_on_violation')->default(true);
            $table->boolean('require_webcam')->default(false);
            $table->boolean('require_fullscreen')->default(true);

            // AI settings
            $table->boolean('ai_generated')->default(false);
            $table->string('source_document_path')->nullable();
            $table->json('ai_generation_settings')->nullable();

            // Status
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->integer('max_attempts')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['access_code', 'status']);
            $table->index(['teacher_id', 'status']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_assignments');
    }
};
