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
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_levels')) {
            Schema::table('academic_levels', function (Blueprint $table) {
                $table->string('name')->after('id');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
        // Check if the table exists before modifying it
        if (Schema::hasTable('academic_levels')) {
            Schema::table('academic_levels', function (Blueprint $table) {
                $table->dropColumn(['name', 'user_id']);
            });
        }
    }
};