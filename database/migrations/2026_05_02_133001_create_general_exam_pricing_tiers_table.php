<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('subject_count')->unique()->comment('Number of subjects this tier applies to');
            $table->decimal('price_per_student', 10, 2)->comment('Online: price per student per subject count');
            $table->decimal('print_flat_rate', 10, 2)->comment('Print: flat rate per subject count');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_pricing_tiers');
    }
};
