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
        Schema::create('parent_student', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents');
            $table->foreignId('student_id')->constrained('students');
            $table->string('relationship');
            $table->timestamps();
        });

        Schema::table('parents', static function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn('student_id');
            $table->dropColumn('relationship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student');
        Schema::table('parents', static function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students');
            $table->string('relationship')->nullable();
        });
    }
};
