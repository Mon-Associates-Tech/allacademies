<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // 'ai_document' (docx/doc/pdf) for now; 'excel' reserved if you later
            // decide to make the Excel path async too.
            $table->string('driver')->default('ai_document');

            // pending -> processing -> completed | failed
            $table->string('status')->default('pending');

            $table->string('file_path');
            $table->string('original_filename')->nullable();

            $table->foreignId('academic_subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_subtopic_id')->nullable()->constrained()->nullOnDelete();

            // Structured results in DocumentAiQuestionImportService's shape:
            // ['multiple_choice' => [...], 'true_false' => [...], 'essay' => [...]]
            $table->json('results')->nullable();
            $table->json('errors')->nullable();
            $table->string('extraction_method')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_import_batches');
    }
};