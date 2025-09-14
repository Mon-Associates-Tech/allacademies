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
            $table->string('conversation_id')->nullable()->after('user_id');
            $table->string('conversation_title')->nullable()->after('conversation_id');
            $table->index(['user_id', 'conversation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_chat_messages', function (Blueprint $table) {
            Schema::table('academic_chat_messages', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'conversation_id']);
                $table->dropColumn(['conversation_id', 'conversation_title']);
            });
        });
    }
};
