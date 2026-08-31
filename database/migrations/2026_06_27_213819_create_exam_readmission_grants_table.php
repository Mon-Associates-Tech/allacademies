<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_readmission_grants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('general_exam_id')
                ->constrained('general_exams')
                ->cascadeOnDelete();

            // The existing submission that the candidate originally made
            $table->foreignId('original_submission_id')
                ->constrained('general_exam_submissions')
                ->cascadeOnDelete();

            // Admin who granted readmission
            $table->foreignId('granted_by')
                ->constrained('users')
                ->restrictOnDelete();

            // 'continue' → resume old submission (responses preserved, timer reset from now)
            // 'fresh'    → create a brand-new submission; old one is marked superseded
            $table->enum('mode', ['continue', 'fresh']);

            $table->text('reason')->nullable();

            // Filled once the candidate actually uses this grant
            $table->timestamp('used_at')->nullable();

            // For fresh mode: the new submission that was created
            $table->foreignId('new_submission_id')
                ->nullable()
                ->constrained('general_exam_submissions')
                ->nullOnDelete();

            // Optional: admin can set a deadline by which the candidate must use the grant
            $table->timestamp('expires_at')->nullable();

            // Soft revocation without deleting the audit record
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('revoke_reason')->nullable();

            $table->timestamps();

            // Only one active (unused, unrevoked) grant per submission at a time
            $table->index(['original_submission_id', 'used_at', 'revoked_at'], 'idx_grants_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_readmission_grants');
    }
};