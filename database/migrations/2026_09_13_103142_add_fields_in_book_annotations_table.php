<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_annotations', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('color');
            $table->string('source', 30)->nullable()->after('external_id');
            $table->index(['book_id', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('book_annotations', function (Blueprint $table) {
            $table->dropIndex(['book_id', 'external_id']);
            $table->dropColumn(['external_id', 'source']);
        });
    }
};