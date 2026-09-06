<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'bookshop_restock_requests';

        Schema::table($table, function (Blueprint $t) use ($table) {
            // What was actually received, distinct from requested_quantity -
            // a shipment can arrive short or damaged. Only meaningful once
            // status reaches CONFIRMED; null before then.
            if (! Schema::hasColumn($table, 'confirmed_quantity')) {
                $t->unsignedInteger('confirmed_quantity')->nullable()->after('requested_quantity');
            }
            if (! Schema::hasColumn($table, 'dispatched_at')) {
                $t->timestamp('dispatched_at')->nullable()->after('reviewed_at');
            }
            if (! Schema::hasColumn($table, 'dispatched_by_staff_id')) {
                $t->foreignId('dispatched_by_staff_id')->nullable()
                    ->after('dispatched_at')->constrained('bookshop_staff')->nullOnDelete();
            }
            if (! Schema::hasColumn($table, 'delivered_at')) {
                $t->timestamp('delivered_at')->nullable()->after('dispatched_by_staff_id');
            }
            if (! Schema::hasColumn($table, 'delivered_by_staff_id')) {
                $t->foreignId('delivered_by_staff_id')->nullable()
                    ->after('delivered_at')->constrained('bookshop_staff')->nullOnDelete();
            }
            if (! Schema::hasColumn($table, 'confirmed_at')) {
                $t->timestamp('confirmed_at')->nullable()->after('delivered_by_staff_id');
            }
            if (! Schema::hasColumn($table, 'confirmed_by_staff_id')) {
                $t->foreignId('confirmed_by_staff_id')->nullable()
                    ->after('confirmed_at')->constrained('bookshop_staff')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookshop_restock_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dispatched_by_staff_id');
            $table->dropConstrainedForeignId('delivered_by_staff_id');
            $table->dropConstrainedForeignId('confirmed_by_staff_id');
            $table->dropColumn(['confirmed_quantity', 'dispatched_at', 'delivered_at', 'confirmed_at']);
        });
    }
};
