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
            if (!Schema::hasColumn('examinations', 'instructions')) {
                $table->longText('instructions')->nullable()->after('heading');
            }
            if (!Schema::hasColumn('examinations', 'duration')) {
                $table->unsignedBigInteger('duration')->default(0)->after('instructions');
            }
            if (!Schema::hasColumn('examinations', 'metadata')) {
                $table->json('metadata')->nullable()->after('duration');
            }
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
