<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', static function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->string('favorite_subjects')->nullable();
            $table->text('learning_goals')->nullable();
            $table->json('social_links')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('students', static function (Blueprint $table) {
            $table->dropColumn(['bio', 'favorite_subjects', 'learning_goals', 'social_links']);
        });
    }
};
