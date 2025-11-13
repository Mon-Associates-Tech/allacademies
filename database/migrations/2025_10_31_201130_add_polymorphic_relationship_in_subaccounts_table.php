<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subaccounts', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('subaccountable_type')->nullable()->after('id');
            $table->unsignedBigInteger('subaccountable_id')->nullable()->after('subaccountable_type');

            // Add index for polymorphic relationship
            $table->index(['subaccountable_type', 'subaccountable_id'], 'subaccountable_index');
        });

        // Migrate existing data: Convert school_id relationships to polymorphic
        DB::table('subaccounts')
            ->whereNotNull('school_id')
            ->update([
                'subaccountable_type' => 'App\\Models\\School',
                'subaccountable_id' => DB::raw('school_id')
            ]);

        // Now we can make school_id nullable (but keep it for backward compatibility if needed)
        Schema::table('subaccounts', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->change();
        });

    }

    public function down(): void
    {
        // Restore school_id data from polymorphic columns
        DB::table('subaccounts')
            ->where('subaccountable_type', 'App\\Models\\School')
            ->update([
                'school_id' => DB::raw('subaccountable_id')
            ]);

        Schema::table('subaccounts', function (Blueprint $table) {
            $table->dropIndex('subaccountable_index');
            $table->dropColumn(['subaccountable_type', 'subaccountable_id']);

            // Make school_id non-nullable again
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
        });
    }
};
