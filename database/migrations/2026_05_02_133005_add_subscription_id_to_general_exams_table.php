<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->unsignedBigInteger('general_exam_subscription_id')->nullable()->after('user_id');
            $table->foreign('general_exam_subscription_id', 'ge_subscription_id_foreign')
                ->references('id')->on('general_exam_subscriptions')->nullOnDelete();
            $table->string('delivery_type')->default('online')->after('type')->comment('online or print');
        });
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropForeign(['general_exam_subscription_id']);
            $table->dropColumn(['general_exam_subscription_id', 'delivery_type']);
        });
    }
};
