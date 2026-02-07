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
        Schema::table('student_id_cards', function (Blueprint $table) {
            if (! Schema::hasColumn('student_id_cards', 'template_used')) {
                $table->string('template_used')->default('professional')->after('status');
            }
            if (! Schema::hasColumn('student_id_cards', 'custom_data')) {
                $table->json('custom_data')->nullable()->after('template_used');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_id_cards', function (Blueprint $table) {
            if (Schema::hasColumn('student_id_cards', 'template_used')) {
                $table->dropColumn('template_used');
            }
            if (Schema::hasColumn('student_id_cards', 'custom_data')) {
                $table->dropColumn('custom_data');
            }
        });
    }
};
