<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->boolean('all_day')->default(false);
            $table->string('color')->nullable();
            $table->string('event_type'); // Type of event (note, assignment, assessment, etc.)
            $table->unsignedBigInteger('event_id'); // ID of the related model
            $table->unsignedBigInteger('user_id'); // Creator of the event
            $table->enum('visibility', ['private', 'public', 'shared'])->default('private');
            $table->json('sharing_settings')->nullable(); // Store sharing configurations
            $table->timestamps();

            $table->index(['user_id', 'start_date']);
            $table->index(['event_type', 'event_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('calendar_events');
    }
};