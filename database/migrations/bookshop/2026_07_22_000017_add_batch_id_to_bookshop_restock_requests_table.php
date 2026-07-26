<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookshop_restock_requests', function (Blueprint $table) {
            $table->uuid('batch_id')->nullable()->after('id');
        });

        // Backfill: every existing row becomes its own single-item batch,
        // since pre-migration data has no record of which items were
        // actually submitted together in one request.
        DB::table('bookshop_restock_requests')->whereNull('batch_id')->orderBy('id')->get(['id'])->each(function ($row) {
            DB::table('bookshop_restock_requests')->where('id', $row->id)->update(['batch_id' => (string) Str::uuid()]);
        });

        Schema::table('bookshop_restock_requests', function (Blueprint $table) {
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookshop_restock_requests', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};
