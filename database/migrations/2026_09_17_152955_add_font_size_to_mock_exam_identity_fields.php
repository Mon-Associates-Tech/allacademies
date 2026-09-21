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
        Schema::table('mock_exam_identity_fields', function (Blueprint $table) {
            $table->unsignedSmallInteger('font_size')->nullable()->after('pretext'); // pt, text fields only
            $table->boolean('same_row')->default(false)->after('sort_order');       // true = share a row with the previous field
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mock_exam_identity_fields', function (Blueprint $table) {
            //
        });
    }
};
