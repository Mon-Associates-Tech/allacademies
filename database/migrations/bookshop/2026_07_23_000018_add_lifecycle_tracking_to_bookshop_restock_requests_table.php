<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookshop_restock_requests', function (Blueprint $table) {
            // What was actually received, distinct from requested_quantity -
            // a shipment can arrive short or damaged. Only meaningful once
            // status reaches CONFIRMED; null before then.
            $table->unsignedInteger('confirmed_quantity')->nullable()->after('requested_quantity');

            $table->timestamp('dispatched_at')->nullable()->after('reviewed_at');
            $table->foreignId('dispatched_by_staff_id')->nullable()
                ->after('dispatched_at')->constrained('bookshop_staff')->nullOnDelete();

            $table->timestamp('delivered_at')->nullable()->after('dispatched_by_staff_id');
            $table->foreignId('delivered_by_staff_id')->nullable()
                ->after('delivered_at')->constrained('bookshop_staff')->nullOnDelete();

            $table->timestamp('confirmed_at')->nullable()->after('delivered_by_staff_id');
            $table->foreignId('confirmed_by_staff_id')->nullable()
                ->after('confirmed_at')->constrained('bookshop_staff')->nullOnDelete();
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
