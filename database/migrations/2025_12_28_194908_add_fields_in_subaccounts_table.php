<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subaccounts', function (Blueprint $table) {
            // Add a name/label for multiple subaccounts
            $table->string('name')->nullable()->after('subaccount_code');
            // Mark primary subaccount
            $table->boolean('is_primary')->default(false)->after('name');
            // Status to manage active/inactive subaccounts
            $table->string('status')->default('active')->after('is_primary');

            // Add unique constraint for primary accounts per model
            $table->unique(
                ['subaccountable_type', 'subaccountable_id', 'is_primary'],
                'unique_primary_subaccount_per_model'
            )->where('is_primary', true);

            // Index for faster queries
            $table->index(['subaccountable_type', 'subaccountable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('subaccounts', function (Blueprint $table) {
            $table->dropUnique('unique_primary_subaccount_per_model');
            $table->dropIndex(['subaccountable_type', 'subaccountable_id']);
            $table->dropColumn(['name', 'is_primary', 'status']);
        });
    }
};
