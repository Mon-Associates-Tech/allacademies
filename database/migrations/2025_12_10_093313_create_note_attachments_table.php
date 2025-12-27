<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_attachments', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filename'); // UUID filename
            $table->string('original_filename');
            $table->string('path');
            $table->unsignedBigInteger('size'); // in bytes
            $table->string('mime_type');
            $table->timestamps();

            $table->index(['note_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_attachments');
    }
};
