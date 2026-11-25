<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Updates all deprecated GPT model references to gpt-6.
     * The deprecated models (gpt-4.1-nano, gpt-4-nano, gpt-4, etc.) were removed
     * by OpenAI and replaced with gpt-6.
     */
    public function up(): void
    {
        $deprecatedModels = [
            'gpt-4.1-nano',
            'gpt-4.1',
            'gpt-4-nano',
            'gpt-4-turbo',
            'gpt-4',
            'gpt-3.5-turbo',
            'gpt-5'
        ];

        // Update pricing_tiers table
        if (Schema::hasTable('pricing_tiers')) {
            DB::table('pricing_tiers')
                ->whereIn('model', $deprecatedModels)
                ->update(['model' => 'gpt-6.2-sol']);
        }

        // Update openai_token_packages table
        if (Schema::hasTable('openai_token_packages')) {
            DB::table('openai_token_packages')
                ->whereIn('model', $deprecatedModels)
                ->update(['model' => 'gpt-6.2-sol']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - gpt-6.2-sol is the correct model
    }
};
