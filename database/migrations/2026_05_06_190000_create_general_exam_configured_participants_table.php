<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_configured_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_exam_id')->constrained('general_exams')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('unique_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['general_exam_id', 'email'], 'gen_exam_cfg_participant_email_unique');
            $table->index(['general_exam_id', 'unique_code'], 'gen_exam_cfg_participant_code_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_configured_participants');
    }
};

