<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Updates existing token packages to link them to pricing tiers
     */
    public function up(): void
    {
        // Update existing Basic package to link to Basic pricing tier
        DB::table('openai_token_packages')
            ->where('name', 'Basic')
            ->update([
                'pricing_tier_id' => DB::table('pricing_tiers')->where('name', 'Basic')->value('id'),
            ]);

        // Update existing Premium package to link to Premium pricing tier
        DB::table('openai_token_packages')
            ->where('name', 'Premium')
            ->update([
                'pricing_tier_id' => DB::table('pricing_tiers')->where('name', 'Premium')->value('id'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('openai_token_packages')->update(['pricing_tier_id' => null]);
    }
};
