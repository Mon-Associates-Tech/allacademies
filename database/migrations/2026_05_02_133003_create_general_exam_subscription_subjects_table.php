<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_subscription_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('general_exam_subscription_id');
            $table->foreign('general_exam_subscription_id', 'gess_subscription_id_foreign')
                ->references('id')->on('general_exam_subscriptions')->cascadeOnDelete();
            $table->foreignId('academic_subject_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['general_exam_subscription_id', 'academic_subject_id'], 'gess_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_subscription_subjects');
    }
};
