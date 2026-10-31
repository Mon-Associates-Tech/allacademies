<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_centers', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 26)->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('token_hash')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_pull_at')->nullable();
            $table->timestamp('last_push_at')->nullable();
            $table->string('app_version', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_centers');
    }
};
