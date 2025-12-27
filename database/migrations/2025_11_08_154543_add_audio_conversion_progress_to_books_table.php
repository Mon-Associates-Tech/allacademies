<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->json('audio_conversion_progress')->nullable()->after('audio_conversion_initiated_by');
            $table->integer('audio_conversion_attempts')->default(0)->after('audio_conversion_progress');
            $table->timestamp('audio_conversion_last_attempt')->nullable()->after('audio_conversion_attempts');
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['audio_conversion_progress', 'audio_conversion_attempts', 'audio_conversion_last_attempt']);
        });
    }
};