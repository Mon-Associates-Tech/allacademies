<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_outline_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_outline_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_subtopic_id')->nullable()->constrained()->onDelete('set null');
            $table->date('planned_date');
            $table->string('teaching_strategy')->nullable();
            $table->text('resources_needed')->nullable();
            $table->text('learning_objectives')->nullable();
            $table->string('assessment_method')->nullable();
            $table->text('notes')->nullable();
            $table->integer('order');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_outline_items');
    }
};
