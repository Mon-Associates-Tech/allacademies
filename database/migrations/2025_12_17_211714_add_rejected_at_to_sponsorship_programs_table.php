<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsorship_programs', static function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('sponsorship_programs', static function (Blueprint $table) {
            $table->dropColumn('rejected_at');
        });
    }
};
