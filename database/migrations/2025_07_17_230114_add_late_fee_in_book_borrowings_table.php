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
        Schema::table('book_borrowings', function (Blueprint $table) {
            $table->decimal('late_fee', 10, 2)->default(0.00)->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_borrowings', function (Blueprint $table) {
            $table->dropColumn('late_fee');
        });
    }
};
