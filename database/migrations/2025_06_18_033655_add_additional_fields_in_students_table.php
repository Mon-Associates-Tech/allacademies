<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', static function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'bio')) {
                $table->text('bio')->nullable();
            }
            if (!Schema::hasColumn('students', 'favorite_subjects')) {
                $table->string('favorite_subjects')->nullable();
            }
            if (!Schema::hasColumn('students', 'learning_goals')) {
                $table->text('learning_goals')->nullable();
            }
            if (!Schema::hasColumn('students', 'social_links')) {
                $table->json('social_links')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', static function (Blueprint $table) {
            $table->dropColumn(['bio', 'favorite_subjects', 'learning_goals', 'social_links']);
        });
    }
};
