<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_assignment_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_assignment_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('order')->default(0);
            $table->integer('time_limit_minutes')->nullable();
            $table->integer('total_marks')->default(0);
            $table->boolean('is_randomized')->default(false);
            $table->timestamps();

            $table->index(['public_assignment_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_assignment_sections');
    }
};
