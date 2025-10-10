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
        Schema::table('academic_periods', function (Blueprint $table) {
            if(Schema::hasIndex('academic_periods', 'unique_current_period')){
                $table->dropIndex('unique_current_period');
            }
            $table->enum('type', ['term', 'semester', 'trimester', 'session', 'year', 'quarter', 'other'])->change();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('academic_year')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_periods', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->string('academic_year')->nullable(false)->change();
            $table->enum('type', ['term', 'semester'])->change();

            $table->dropColumn('academic_year_id');
        });
    }
};
