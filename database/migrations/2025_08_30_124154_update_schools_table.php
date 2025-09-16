<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Add new fields for multi-tenancy
            $table->string('code', 50)->unique()->after('name');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('logo');
            $table->string('subscription_plan', 50)->nullable()->after('status');
            $table->json('settings')->nullable()->after('subscription_plan');
            $table->timestamp('subscription_ends_at')->nullable()->after('settings');

            // Update existing fields
            $table->string('phone', 20)->nullable()->change();
            $table->string('logo', 500)->nullable()->change();

            // Add indexes
            $table->index(['status', 'subscription_ends_at']);
            $table->index('code');
        });
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['status', 'subscription_ends_at']);
            $table->dropIndex(['code']);
            $table->dropColumn([
                'code', 'status', 'subscription_plan',
                'settings', 'subscription_ends_at'
            ]);
        });
    }
};
