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
        Schema::create('parents', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('relationship')->nullable(); // e.g., 'mother', 'father', 'guardian', etc.
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['student_id']);
            $table->index(['user_id']);
            $table->index(['relationship']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
