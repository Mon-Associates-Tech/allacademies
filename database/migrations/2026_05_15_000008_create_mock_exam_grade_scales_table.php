<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_exam_grade_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('grade_label', 10);
            $table->unsignedTinyInteger('min_percentage');
            $table->unsignedTinyInteger('max_percentage');
            $table->decimal('grade_point', 4, 2)->nullable();
            $table->boolean('is_passing')->default(true);
            $table->string('color_code', 20)->default('#6B7280');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_exam_grade_scales');
    }
};
