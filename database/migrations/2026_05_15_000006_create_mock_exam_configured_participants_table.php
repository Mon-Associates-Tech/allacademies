<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_exam_configured_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('unique_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['mock_exam_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_exam_configured_participants');
    }
};
