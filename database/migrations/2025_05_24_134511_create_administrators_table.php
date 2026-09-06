<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the table exists before creating it
        if (!Schema::hasTable('administrators')) {
            Schema::create('administrators', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Check if the table exists before dropping it
        if (Schema::hasTable('administrators')) {
            Schema::dropIfExists('administrators');
        }
    }
};
