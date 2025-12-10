<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('note_shares', static function (Blueprint $table) {
            $table->string('guest_email')->nullable()->after('shared_with_user_id');
            $table->index('guest_email');
        });
    }

    public function down(): void
    {
        Schema::table('note_shares', static function (Blueprint $table) {
            $table->dropColumn('guest_email');
        });
    }
};
