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
        Schema::table('librarians', function (Blueprint $table) {
            if (!Schema::hasColumn('librarians', 'position')) {
                $table->string('position')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('librarians', 'department')) {
                $table->string('department')->nullable()->after('position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('librarians', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('department');
        });
    }
};
