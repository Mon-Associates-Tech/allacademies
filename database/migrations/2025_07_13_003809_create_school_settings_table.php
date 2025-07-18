<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', static function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('type')->default('text'); // text, longtext, image, JSON, PDF, boolean, number
            $table->longText('value')->nullable();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('group')->default('general'); // general, appearance, academic, etc.
            $table->json('options')->nullable(); // For select/radio options
            $table->boolean('required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
