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
        if (Schema::hasTable('examinations')) {
            Schema::table('examinations', function (Blueprint $table) {
                $table->dropColumn('examiners');
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
        if (Schema::hasTable('examinations')) {
            Schema::table('examinations', function (Blueprint $table) {
                $table->string('examiners')->nullable();
            });
        }
    }
};
