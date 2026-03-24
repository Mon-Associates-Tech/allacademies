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
        Schema::table('schools', function (Blueprint $table) {
            if (! Schema::hasColumn('schools', 'id_card_template')) {
                $table->string('id_card_template')->default('professional')->after('letterhead_template');
            }
            if (! Schema::hasColumn('schools', 'id_card_custom_fields')) {
                $table->json('id_card_custom_fields')->nullable()->after('id_card_template');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'id_card_template')) {
                $table->dropColumn('id_card_template');
            }
            if (Schema::hasColumn('schools', 'id_card_custom_fields')) {
                $table->dropColumn('id_card_custom_fields');
            }
        });
    }
};
