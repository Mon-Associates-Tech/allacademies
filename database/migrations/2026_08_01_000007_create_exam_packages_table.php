<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_packages', function (Blueprint $table) {
            $table->id();
            $table->string('exam_uuid', 26);
            $table->unsignedInteger('revision');
            $table->longText('payload'); // gzip compressed JSON
            $table->string('checksum', 64); // sha256
            $table->string('signature', 256); // Ed25519 base64
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['exam_uuid', 'revision']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_packages');
    }
};
