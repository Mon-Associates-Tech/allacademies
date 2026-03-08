<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Inserts the default pricing tiers:
     * - Basic: $10/month for first 6 months, then $5/month
     * - Premium: $15/month for first 6 months, then $10/month
     *
     * Note: You should define the monthly_token_limit based on your requirements
     */
    public function up(): void
    {
        DB::table('pricing_tiers')->insert([
            [
                'name' => 'Basic',
                'model' => 'gpt-4-nano',
                'initial_price' => 10.00,
                'subsequent_price' => 5.00,
                'monthly_token_limit' => 50000, // Adjust as needed
                'initial_period_months' => 6,
                'description' => 'Basic tier with incremental pricing. $10/month for first 6 months, then $5/month. 50K tokens per month.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'model' => 'gpt-4-turbo',
                'initial_price' => 15.00,
                'subsequent_price' => 10.00,
                'monthly_token_limit' => 70000, // Adjust as needed
                'initial_period_months' => 6,
                'description' => 'Premium tier with incremental pricing. $15/month for first 6 months, then $10/month. 70K tokens per month.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('pricing_tiers')->whereIn('name', ['Basic', 'Premium'])->delete();
    }
};
