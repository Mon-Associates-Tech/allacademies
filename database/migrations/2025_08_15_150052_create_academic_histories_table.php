<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', [
                'level_change',
                'assessment',
                'achievement',
                'attendance',
                'behavior',
                'award',
                'certification',
                'milestone',
                'other'
            ]);
            $table->foreignId('recorded_by_id')->constrained('users');
            $table->datetime('recorded_date');
            $table->string('academic_period')->nullable();
            $table->decimal('achievement_score', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_histories');
    }
};
