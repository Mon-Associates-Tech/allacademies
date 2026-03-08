<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'start_time')) {
                $table->timestamp('start_time')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'end_time')) {
                $table->timestamp('end_time')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'duration')) {
                $table->string('duration')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'topic_id')) {
                $table->unsignedBigInteger('topic_id')->nullable();
            }
            if (!Schema::hasColumn('assessments', 'subtopic_id')) {
                $table->unsignedBigInteger('subtopic_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
