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
        Schema::table('examinations', static function (Blueprint $table) {
            $table->longText('instructions')->nullable()->after('heading');
            $table->unsignedBigInteger('duration')->default(0)->after('instruction');
            $table->json('metadata')->nullable()->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examinations', static function (Blueprint $table) {
            $table->dropColumn(['instructions', 'duration', 'metadata']);
        });
    }
};
