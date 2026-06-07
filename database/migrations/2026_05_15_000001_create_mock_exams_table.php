<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->string('access_code', 8)->nullable()->unique();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->enum('delivery_type', ['online', 'print'])->default('online');
            $table->enum('participant_mode', ['general', 'configured'])->default('general');
            $table->enum('configured_match_mode', ['any', 'both'])->default('any');
            $table->json('participant_required_fields')->nullable();
            $table->boolean('email_verification_required')->default(false);
            $table->enum('result_visibility', [
                'immediate', 'after_due_date', 'manual_release', 'scheduled',
            ])->default('manual_release');
            $table->timestamp('results_release_datetime')->nullable();
            $table->boolean('results_released')->default(false);
            $table->timestamp('results_released_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_randomized')->default(false);
            $table->unsignedSmallInteger('max_attempts')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_exams');
    }
};
