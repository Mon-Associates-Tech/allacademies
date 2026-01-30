<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', static function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'annual_subscription_fee')) {
                $table->decimal('annual_subscription_fee', 8, 2)->default(50.00)->after('content_url');
            }
            if (!Schema::hasColumn('books', 'subscription_conditions')) {
                $table->text('subscription_conditions')->nullable()->after('annual_subscription_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', static function (Blueprint $table) {
            $table->dropColumn(['annual_subscription_fee', 'subscription_conditions']);
        });
    }
};
