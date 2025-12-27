<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_sessions', function (Blueprint $table) {
            // Recurring fields
            $table->boolean('is_recurring')->default(false)->after('type');
            $table->string('recurrence_pattern')->nullable()->after('is_recurring'); // daily, weekly, monthly
            $table->json('recurrence_days')->nullable()->after('recurrence_pattern'); // [1,3,5] for Mon, Wed, Fri
            $table->integer('recurrence_interval')->default(1)->after('recurrence_days'); // Every X weeks/months
            $table->dateTime('recurrence_end_date')->nullable()->after('recurrence_interval');
            $table->boolean('recurrence_active')->default(true)->after('recurrence_end_date');
            $table->foreignId('parent_session_id')->nullable()->after('recurrence_active')->constrained('virtual_sessions')->nullOnDelete();

            // Index for better performance
            $table->index(['is_recurring', 'recurrence_active']);
            $table->index('parent_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('virtual_sessions', function (Blueprint $table) {
            $table->dropForeign(['parent_session_id']);
            $table->dropIndex(['is_recurring', 'recurrence_active']);
            $table->dropIndex(['parent_session_id']);

            $table->dropColumn([
                'is_recurring',
                'recurrence_pattern',
                'recurrence_days',
                'recurrence_interval',
                'recurrence_end_date',
                'recurrence_active',
                'parent_session_id',
            ]);
        });
    }
};
