<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_sync_state', function (Blueprint $table) {
            $table->id();
            $table->string('exam_uuid', 26)->unique();
            $table->unsignedInteger('applied_revision')->default(0);
            $table->unsignedInteger('applied_state_version')->default(0);
            $table->string('checksum', 64)->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_sync_state');
    }
};
