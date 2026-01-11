<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        // Get all active legacy subscriptions
        $activeSubscriptions = DB::table('user_token_subscriptions')
            ->where('status', 'active')
            ->whereNotNull('activated_at')
            ->get();

        foreach ($activeSubscriptions as $subscription) {
            try {
                // Get pricing tier based on package
                $package = DB::table('openai_token_packages')
                    ->where('id', $subscription->package_id)
                    ->first();

                if (!$package) {
                    Log::warning('Package not found for subscription', ['subscription_id' => $subscription->id]);
                    continue;
                }

                // Determine pricing tier (Basic or Premium)
                $tierName = ($package->name === 'Premium' || $package->price > 10) ? 'Premium' : 'Basic';
                
                $pricingTier = DB::table('pricing_tiers')
                    ->where('name', $tierName)
                    ->where('is_active', true)
                    ->first();

                if (!$pricingTier) {
                    Log::warning('Pricing tier not found', ['tier_name' => $tierName]);
                    continue;
                }

                // Check if user already has an active cycle
                $existingCycle = DB::table('subscription_cycles')
                    ->where('user_id', $subscription->user_id)
                    ->where('status', 'active')
                    ->first();

                if ($existingCycle) {
                    Log::info('User already has active cycle, skipping', ['user_id' => $subscription->user_id]);
                    continue;
                }

                // Create subscription cycle
                DB::table('subscription_cycles')->insert([
                    'user_id' => $subscription->user_id,
                    'pricing_tier_id' => $pricingTier->id,
                    'cycle_number' => 1,
                    'cycle_start_date' => $subscription->activated_at ?? now(),
                    'cycle_end_date' => $subscription->expires_at ?? now()->addDays(30),
                    'tokens_allocated' => $subscription->tokens_remaining,
                    'topup_tokens_allocated' => 0,
                    'tokens_used' => $subscription->tokens_used,
                    'current_price' => $package->price ?? $pricingTier->initial_price,
                    'status' => 'active',
                    'is_topup' => false,
                    'is_merged' => false,
                    'merged_with_group_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Migrated subscription to cycle', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'tier' => $tierName,
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to migrate subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback - keep both systems for safety
    }
};
