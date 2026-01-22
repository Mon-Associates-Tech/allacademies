<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reading_achievements', function (Blueprint $table) {
            if (! Schema::hasColumn('reading_achievements', 'type')) {
                $table->string('type');
            }
            if (! Schema::hasColumn('reading_achievements', 'name')) {
                $table->string('name');
            }
            if (! Schema::hasColumn('reading_achievements', 'description')) {
                $table->text('description');
            }
            if (! Schema::hasColumn('reading_achievements', 'criteria')) {
                $table->json('criteria')->nullable();
            }
            if (! Schema::hasColumn('reading_achievements', 'awarded_at')) {
                $table->timestamp('awarded_at')->nullable();
            }
            if (! Schema::hasColumn('reading_achievements', 'user_id')) {
                $table->unsignedBigInteger('user_id');
            }

            // Add timestamps if they don't exist
            if (! Schema::hasColumn('reading_achievements', 'created_at') && ! Schema::hasColumn('reading_achievements', 'updated_at')) {
                $table->timestamps();
            }

            // Add index if it doesn't exist
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_achievements', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'type']);
            $table->dropColumn(['type', 'name', 'description', 'criteria', 'awarded_at', 'user_id']);
        });
    }
};
