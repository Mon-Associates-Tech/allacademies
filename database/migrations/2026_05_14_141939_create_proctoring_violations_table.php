<?php
/**
 * Migration for Proctoring Violations
 *
 * Stores individual violation events detected during a proctored exam.
 * Includes violation type, severity, metadata payload, and links to session.
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_proctoring_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_proctoring_session_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->json('metadata')->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_proctoring_violations');
    }
};
