<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->string('participant_mode')->default('general')->after('delivery_type');
            $table->json('participant_required_fields')->nullable()->after('participant_mode');
            $table->string('configured_match_mode')->default('any')->after('participant_required_fields');
        });
    }

    public function down(): void
    {
        Schema::table('general_exams', function (Blueprint $table) {
            $table->dropColumn(['participant_mode', 'participant_required_fields', 'configured_match_mode']);
        });
    }
};

