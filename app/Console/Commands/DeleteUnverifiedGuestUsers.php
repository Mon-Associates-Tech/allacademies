<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class DeleteUnverifiedGuestUsers extends Command
{
    protected $signature = 'users:delete-unverified-guests {months=24 : Number of months to consider account as old}';

    protected $description = 'Delete unverified guest users whose accounts are older than specified months';

    public function handle(): int
    {
        $months = (int) $this->argument('months');
        
        $cutoffDate = now()->subMonths($months);
        
        $count = User::where('role', UserRole::GUEST->value)
            ->whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
        
        $this->info("Deleted {$count} unverified guest user(s) older than {$months} months.");
        
        return Command::SUCCESS;
    }
}
