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
        Schema::table('academic_topics', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name')->nullable();
        });
        Schema::table('academic_subtopics', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name')->nullable();
        });
        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_topics', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
        Schema::table('academic_subtopics', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
    }
};
