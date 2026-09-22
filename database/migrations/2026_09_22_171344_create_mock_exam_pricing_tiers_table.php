<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('mock_exam_pricing_tiers')) {
            Schema::create('mock_exam_pricing_tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->unsignedInteger('subject_count');
                $table->decimal('price_per_student', 10, 2)->default(0);
                $table->decimal('print_flat_rate', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('subject_count');
                $table->index('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_exam_pricing_tiers');
    }
};
