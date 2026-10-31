<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_exam_submissions', 'center_id')) {
            Schema::table('general_exam_submissions', function (Blueprint $table) {
                $table->unsignedBigInteger('center_id')->nullable()->after('uuid');
                $table->string('participant_index_number', 50)->nullable()->after('center_id');
                $table->unsignedInteger('graded_revision')->nullable()->after('participant_index_number');
                $table->unsignedInteger('sync_version')->default(0)->after('graded_revision');
                $table->unsignedInteger('synced_version')->default(0)->after('sync_version');

                // Central-only columns for parity checking
                $table->decimal('central_score', 8, 2)->nullable()->after('synced_version');
                $table->boolean('parity_ok')->nullable()->after('central_score');
                $table->timestamp('parity_checked_at')->nullable()->after('parity_ok');

                $table->index('center_id');
                $table->index('participant_index_number');
                $table->index(['sync_version', 'synced_version']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('general_exam_submissions', function (Blueprint $table) {
            $table->dropIndex(['general_exam_submissions_center_id']);
            $table->dropIndex(['general_exam_submissions_participant_index_number']);
            $table->dropIndex(['general_exam_submissions_sync_version_synced_version']);
            $table->dropColumn([
                'center_id', 'participant_index_number', 'graded_revision',
                'sync_version', 'synced_version', 'central_score',
                'parity_ok', 'parity_checked_at',
            ]);
        });
    }
};
