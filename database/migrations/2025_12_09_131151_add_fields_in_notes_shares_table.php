<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('note_shares', function (Blueprint $table) {
            // Add share_type to distinguish different sharing methods
            $table->string('share_type')->default('individual')->after('note_id');
            // individual, academic_group, academic_level, student_group, school_wide

            // Make shared_with_user_id nullable since we might share with groups
            $table->unsignedBigInteger('shared_with_user_id')->nullable()->change();

            // Add polymorphic relationship for flexible sharing
            $table->string('shareable_type')->nullable()->after('share_type');
            $table->unsignedBigInteger('shareable_id')->nullable()->after('shareable_type');

            // Add notification tracking
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notified_at')->nullable();

            $table->index(['shareable_type', 'shareable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('note_shares', function (Blueprint $table) {
            $table->dropColumn([
                'share_type',
                'shareable_type',
                'shareable_id',
                'notification_sent',
                'notified_at'
            ]);
        });
    }
};
