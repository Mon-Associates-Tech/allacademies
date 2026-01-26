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
        Schema::table('academic_chat_messages', static function (Blueprint $table) {
            $table->string('model_used')->nullable()->after('usage');
            $table->json('images')->nullable()->after('model_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_chat_messages', static function (Blueprint $table) {
            $table->dropIndex(['user_id', 'conversation_id']);
            $table->dropColumn(['model_used', 'images']);
        });
    }
};
