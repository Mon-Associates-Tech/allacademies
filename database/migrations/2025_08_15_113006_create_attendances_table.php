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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_level_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_subject_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('session', ['morning', 'afternoon', 'full_day'])->default('morning');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['teacher_id', 'date']);
            $table->index(['academic_level_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
