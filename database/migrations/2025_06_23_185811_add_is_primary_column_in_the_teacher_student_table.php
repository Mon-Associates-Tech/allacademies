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
        Schema::table('teacher_student', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false);
            $table->string('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_student', function (Blueprint $table) {
            $table->dropColumn('is_primary');
            $table->dropColumn('notes');
        });
    }
};
