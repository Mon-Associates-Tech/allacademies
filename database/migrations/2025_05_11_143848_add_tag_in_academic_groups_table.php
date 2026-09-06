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
        if (Schema::hasTable('academic_groups')) {
            Schema::table('academic_groups', function (Blueprint $table) {
                if (!Schema::hasColumn('academic_groups', 'tag')) {
                    $table->string('tag')->after('name')->nullable()->default('basic');
                }
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
        if (Schema::hasTable('academic_groups')) {
            Schema::table('academic_groups', function (Blueprint $table) {
                $table->dropColumn('tag');
            });
        }
    }
};
