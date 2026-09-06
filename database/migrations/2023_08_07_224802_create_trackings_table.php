<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table exists before creating it
        if (!Schema::hasTable('trackings')) {
            Schema::create('trackings', function (Blueprint $table) {
                $table->id();
                $table->string('event');
                $table->nullableNumericMorphs('trackable');
                $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('snapshot');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the table exists before dropping it
        if (Schema::hasTable('trackings')) {
            Schema::dropIfExists('trackings');
        }
    }
};