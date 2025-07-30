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
        Schema::table('books', function (Blueprint $table) {
            $table->json('table_of_contents')->nullable()->after('additional_info');
            $table->decimal('average_rating', 3, 2)->default(0)->after('table_of_contents');
            $table->unsignedInteger('total_reviews')->default(0)->after('average_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['table_of_contents', 'average_rating', 'total_reviews']);
        });
    }
};
