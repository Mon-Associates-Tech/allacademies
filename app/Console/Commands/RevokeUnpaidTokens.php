<?php

namespace App\Console\Commands;

use App\Models\Chat\SubscriptionCycle;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RevokeUnpaidTokens extends Command
{
    protected $signature = 'tokens:revoke-unpaid {--dry-run : Show what would be revoked without actually revoking}';

    protected $description = 'Revoke tokens from users who have active cycles without corresponding payments';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Scanning for unpaid active subscription cycles...');

        // Find active or inactive cycles that don't have a successful payment
        $suspiciousCycles = SubscriptionCycle::whereIn('status', ['active', 'inactive'])
            ->where('tokens_allocated', '>', 0)
            ->whereNotNull('subscription_group_id')
            ->where('is_trial', false)
            ->where('allocated_by_admin', false)
            ->get()
            ->filter(function ($cycle) {
                // Check if there's a successful payment for this cycle
                $hasPayment = Payment::where('status', 'succeeded')
                    ->where('created_at', '>=', $cycle->created_at->subMinutes(5))
                    ->where('created_at', '<=', $cycle->created_at->addMinutes(30))
                    ->exists();

                return !$hasPayment;
            });

        if ($suspiciousCycles->isEmpty()) {
            $this->info('No unpaid cycles found. All cycles have corresponding payments.');
            return 0;
        }

        $this->warn("Found {$suspiciousCycles->count()} unpaid cycles:");

        $table = [];
        foreach ($suspiciousCycles as $cycle) {
            $table[] = [
                'ID' => $cycle->id,
                'User' => $cycle->user->name,
                'Email' => $cycle->user->email,
                'Tier' => $cycle->pricingTier->name,
                'Tokens' => number_format($cycle->tokens_allocated),
                'Status' => $cycle->status,
                'Created' => $cycle->created_at->format('Y-m-d H:i'),
            ];
        }

        $this->table(['ID', 'User', 'Email', 'Tier', 'Tokens', 'Status', 'Created'], $table);

        if ($isDryRun) {
            $this->info('DRY RUN: No changes made. Run without --dry-run to revoke tokens.');
            return 0;
        }

        if (!$this->confirm('Do you want to revoke tokens from these cycles?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $revokedCount = 0;
        DB::transaction(function () use ($suspiciousCycles, &$revokedCount) {
            foreach ($suspiciousCycles as $cycle) {
                $cycle->update([
                    'status' => 'cancelled',
                    'tokens_allocated' => 0,
                    'tokens_used' => 0,
                    'topup_tokens_allocated' => 0,
                ]);
                $revokedCount++;
            }
        });

        $this->info("Successfully revoked tokens from {$revokedCount} cycles.");
        return 0;
    }
}
