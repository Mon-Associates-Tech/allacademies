<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {if (!Schema::hasTable('assessments')) {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
    }

    public function down(): void
    {
        // Check if the table exists before dropping it
        if (Schema::hasTable('assessments')) {
            Schema::dropIfExists('assessments');
        }
    }
};
