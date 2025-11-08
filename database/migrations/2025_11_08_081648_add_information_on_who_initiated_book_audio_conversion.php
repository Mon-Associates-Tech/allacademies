<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('audio_conversion_initiated_by')->nullable()->after('audio_conversion_pending');
            $table->foreign('audio_conversion_initiated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['audio_conversion_initiated_by']);
            $table->dropColumn('audio_conversion_initiated_by');
        });
    }
};
