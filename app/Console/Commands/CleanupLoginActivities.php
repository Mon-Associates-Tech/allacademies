<?php

namespace App\Console\Commands;

use App\Models\LoginActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupLoginActivities extends Command
{
    protected $signature = 'login:cleanup';
    protected $description = 'Clean up duplicate and inconsistent login activity records';

    public function handle()
    {
        $this->info('Starting login activities cleanup...');

        // 1. Remove exact duplicates
        $this->info('Removing exact duplicates...');
        DB::statement('
            DELETE t1 FROM login_activities t1
            INNER JOIN login_activities t2
            WHERE t1.id < t2.id
            AND t1.user_id = t2.user_id
            AND t1.session_id = t2.session_id
            AND t1.login_at = t2.login_at
        ');

        // 2. Fix records with missing login_at (use created_at)
        $this->info('Fixing missing login_at timestamps...');
        LoginActivity::whereNull('login_at')
            ->update(['login_at' => DB::raw('created_at')]);

        // 3. Calculate missing duration_minutes for completed sessions
        $this->info('Calculating missing durations...');
        LoginActivity::whereNotNull('logout_at')
            ->whereNull('duration_minutes')
            ->update([
                'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, login_at, logout_at)')
            ]);

        // 4. Close orphaned active sessions (older than 24 hours)
        $this->info('Closing orphaned active sessions...');
        $orphaned = LoginActivity::whereNull('logout_at')
            ->where('login_at', '<', now()->subDay())
            ->get();

        foreach ($orphaned as $session) {
            $duration = now()->diffInMinutes($session->login_at);
            $session->update([
                'logout_at' => $session->login_at->addHours(2),
                'action' => 'logged_out',
                'duration_minutes' => min($duration, 120),
                'logout_type' => 'session_timeout'
            ]);
        }

        $this->info('Cleanup completed successfully!');
        return 0;
    }
}
