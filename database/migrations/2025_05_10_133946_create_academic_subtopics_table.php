<?php

use App\Models\AcademicTopic;
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
        if (!Schema::hasTable('academic_subtopics')) {
            Schema::create('academic_subtopics', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignIdFor(AcademicTopic::class)->constrained()->cascadeOnDelete();
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
        if (Schema::hasTable('academic_subtopics')) {
            Schema::dropIfExists('academic_subtopics');
        }
    }
};
