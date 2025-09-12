<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('changelogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('task_name');
            $table->text('task_description');
            $table->text('additional_info')->nullable();
            $table->json('completed_items'); // Store array of completed items
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('changelogs');
    }
};
