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
        Schema::table('school_fees', function (Blueprint $table) {
              $table->integer('term_id')->nullable()->after('id'); // or after another column
              $table->decimal('term_total_amount', 10, 2)->default(0.00)->after('term_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_fees', function (Blueprint $table) {
              $table->dropColumn(['term_id', 'term_total_amount']);
        });
    }
};
