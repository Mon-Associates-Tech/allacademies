<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('general_exam_subscription_plan_id');
            $table->foreign('general_exam_subscription_plan_id', 'ges_plan_id_foreign')
                ->references('id')->on('general_exam_subscription_plans')->cascadeOnDelete();
            $table->string('type')->comment('online or print');
            $table->string('status')->default('pending')->comment('pending, active, expired, cancelled');
            $table->unsignedSmallInteger('participant_slots')->default(0)->comment('Online: total allowed participants');
            $table->unsignedSmallInteger('participants_used')->default(0);
            $table->unsignedSmallInteger('exams_used')->default(0);
            $table->unsignedSmallInteger('max_exams')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->boolean('granted_by_owner')->default(false);
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_subscriptions');
    }
};
