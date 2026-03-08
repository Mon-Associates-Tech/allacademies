<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('teams', 'type')) {
                $table->enum('type', ['academic', 'professional', 'personal'])->default('academic')->after('description');
            }
            if (!Schema::hasColumn('teams', 'privacy')) {
                $table->enum('privacy', ['private', 'public'])->default('private')->after('type');
            }
            if (!Schema::hasColumn('teams', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('privacy');
            }
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['description', 'type', 'privacy', 'is_active']);
        });
    }
};
