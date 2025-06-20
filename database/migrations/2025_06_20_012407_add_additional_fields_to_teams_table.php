<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->enum('type', ['academic', 'professional', 'personal'])->default('academic')->after('description');
            $table->enum('privacy', ['private', 'public'])->default('private')->after('type');
            $table->boolean('is_active')->default(true)->after('privacy');
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['description', 'type', 'privacy', 'is_active']);
        });
    }
};
