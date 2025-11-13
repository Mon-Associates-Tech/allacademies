<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('subscribed_by')->nullable()->after('user_id');
            $table->foreign('subscribed_by')->references('id')->on('users')->onDelete('set null');

            // Add index for better query performance
            $table->index('subscribed_by');
        });
    }

    public function down(): void
    {
        Schema::table('book_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['subscribed_by']);
            $table->dropIndex(['subscribed_by']);
            $table->dropColumn('subscribed_by');
        });
    }
};
