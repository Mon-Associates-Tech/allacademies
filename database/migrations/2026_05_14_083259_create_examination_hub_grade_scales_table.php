<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('examination_hub_grade_scales')) {
            Schema::create('examination_hub_grade_scales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
                $table->string('grade_label', 10);           // A+, A, B+, B …
                $table->unsignedTinyInteger('min_percentage');
                $table->unsignedTinyInteger('max_percentage');
                $table->decimal('grade_point', 3, 2)->nullable(); // e.g. 4.00
                $table->boolean('is_passing')->default(true);
                $table->string('color_code', 20)->default('#6B7280');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['user_id', 'is_active']);
                $table->index(['school_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('examination_hub_grade_scales');
    }
};