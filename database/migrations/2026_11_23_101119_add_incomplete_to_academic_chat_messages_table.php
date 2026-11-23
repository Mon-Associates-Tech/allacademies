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
        Schema::table('academic_chat_messages', function (Blueprint $table) {
            $table->boolean('incomplete')->default(false)->after('images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_chat_messages', function (Blueprint $table) {
            $table->dropColumn('incomplete');
        });
    }
};
