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
        Schema::table('assignment_sections', function (Blueprint $table) {
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay', 'mixed'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_sections', function (Blueprint $table) {
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay'])->change();
        });
    }
};
