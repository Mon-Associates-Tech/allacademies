<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->comment('online or print');
            $table->unsignedTinyInteger('max_subjects')->default(1)->comment('Max subjects allowed in this plan');
            $table->unsignedSmallInteger('max_exams')->nullable()->comment('Max exams allowed; null = unlimited');
            $table->unsignedSmallInteger('max_participants')->nullable()->comment('Online only: max participants; null = unlimited');
            $table->string('duration_type')->default('one_time')->comment('one_time, fixed_count, period');
            $table->unsignedSmallInteger('duration_value')->nullable()->comment('For fixed_count: number of uses; for period: days');
            $table->decimal('base_price', 10, 2)->default(0)->comment('Override price; 0 = calculated from tiers');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_subscription_plans');
    }
};
