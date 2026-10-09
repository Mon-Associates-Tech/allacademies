<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->longText('title')->change();
            $table->longText('instructions')->nullable()->change();
        });

        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->longText('title')->change();
            $table->longText('instructions')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('instructions')->nullable()->change();
        });

        Schema::table('mock_exam_sections', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('instructions')->nullable()->change();
        });
    }
};
