<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_exams', 'revision')) {
            Schema::table('general_exams', function (Blueprint $table) {
                $table->unsignedInteger('revision')->default(1)->after('uuid');
                $table->unsignedInteger('state_version')->default(1)->after('revision');
                $table->json('grading_scale')->nullable()->after('state_version');
                $table->timestamp('published_at')->nullable()->after('grading_scale');
            });
        }
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropColumn(['revision', 'state_version', 'grading_scale', 'published_at']);
        });
    }
};
