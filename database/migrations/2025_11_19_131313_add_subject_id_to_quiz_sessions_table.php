<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_sessions', function (Blueprint $table) {
            // Make book_id nullable
            $table->unsignedBigInteger('book_id')->nullable()->change();

            // Add subject_id
            $table->unsignedBigInteger('subject_id')->nullable()->after('book_id');
            $table->foreign('subject_id')->references('id')->on('academic_subjects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');

            // Revert book_id to non-nullable (if needed)
            $table->unsignedBigInteger('book_id')->nullable(false)->change();
        });
    }
};
