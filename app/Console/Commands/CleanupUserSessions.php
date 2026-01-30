<?php

namespace App\Console\Commands;

use App\Models\UserLogin;
use App\Services\UserLoginService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupUserSessions extends Command
{
    protected $signature = 'sessions:cleanup {--timeout=30 : Session timeout in minutes} {--force : Force cleanup without confirmation}';

    protected $description = 'Cleanup stale user sessions and mark them as timed out';

    private UserLoginService $loginService;

    public function __construct(UserLoginService $loginService)
    {
        parent::__construct();
        $this->loginService = $loginService;
    }

    public function handle()
    {
        $timeoutMinutes = $this->option('timeout');
        $force = $this->option('force');
        $cutoffTime = Carbon::now()->subMinutes($timeoutMinutes);

        $this->info("Starting session cleanup with {$timeoutMinutes} minute timeout...");

        // First, clean up sessions that don't have corresponding Laravel sessions
        $this->cleanupOrphanedSessions($force);

        // Find active sessions that haven't been updated recently
        $staleSessions = UserLogin::activeSessions()
            ->where('login_at', '<', $cutoffTime)
            ->get();

        $cleanedCount = 0;

        if ($staleSessions->count() > 0) {
            $this->info("Found {$staleSessions->count()} stale sessions to clean up.");

            if (! $force && ! $this->confirm('Do you want to proceed with cleanup?')) {
                $this->info('Cleanup cancelled.');

                return 0;
            }

            foreach ($staleSessions as $session) {
                $this->loginService->handleSessionTimeout($session->user_id);
                $cleanedCount++;
            }
        }

        $this->info("Cleaned up {$cleanedCount} stale sessions.");

        // Show current stats
        $this->showCurrentStats();

        return 0;
    }

    private function cleanupOrphanedSessions($force = false)
    {
        $sessionDriver = config('session.driver');
        $this->info("Session driver: {$sessionDriver}");

        $orphanedCount = 0;

        switch ($sessionDriver) {
            case 'database':
                // Get all active UserLogin sessions
                $activeUserLogins = UserLogin::activeSessions()->pluck('session_id')->toArray();

                // Get all Laravel sessions
                $activeLaravelSessions = DB::table('sessions')->pluck('id')->toArray();

                // Find UserLogin sessions that don't have corresponding Laravel sessions
                $orphanedSessions = UserLogin::activeSessions()
                    ->whereNotIn('session_id', $activeLaravelSessions)
                    ->get();

                if ($orphanedSessions->count() > 0) {
                    $this->warn("Found {$orphanedSessions->count()} orphaned user login sessions.");

                    if ($force || $this->confirm('Clean up orphaned sessions?')) {
                        foreach ($orphanedSessions as $session) {
                            $logoutTime = now();
                            $duration = $logoutTime->diffInMinutes($session->login_at);

                            $session->update([
                                'action' => 'logged_out',
                                'logout_at' => $logoutTime,
                                'duration_minutes' => max(0, $duration),
                                'logout_type' => 'session_cleanup',
                            ]);

                            // Update user status if this was their last active session
                            $remainingActiveSessions = UserLogin::where('user_id', $session->user_id)
                                ->whereNull('logout_at')
                                ->count();

                            if ($remainingActiveSessions === 0) {
                                $session->user->update([
                                    'is_online' => false,
                                    'last_seen_at' => now(),
                                ]);
                            }

                            $orphanedCount++;
                        }
                        $this->info("Cleaned up {$orphanedCount} orphaned sessions.");
                    }
                }
                break;

            case 'file':
                // For file sessions, check if session files exist
                $sessionPath = storage_path('framework/sessions');
                if (! is_dir($sessionPath)) {
                    $this->warn("Session directory not found: {$sessionPath}");
                    break;
                }

                $orphanedSessions = UserLogin::activeSessions()->get()->filter(function ($session) use ($sessionPath) {
                    $sessionFile = $sessionPath.'/sess_'.$session->session_id;

                    return ! file_exists($sessionFile);
                });

                if ($orphanedSessions->count() > 0) {
                    $this->warn("Found {$orphanedSessions->count()} orphaned user login sessions (no session files).");

                    if ($force || $this->confirm('Clean up orphaned sessions?')) {
                        foreach ($orphanedSessions as $session) {
                            $this->loginService->handleSessionTimeout($session->user_id);
                            $orphanedCount++;
                        }
                        $this->info("Cleaned up {$orphanedCount} orphaned sessions.");
                    }
                }
                break;
        }
    }

    private function showCurrentStats()
    {
        $stats = [
            'total_sessions' => UserLogin::count(),
            'active_sessions' => UserLogin::activeSessions()->count(),
            'unique_active_users' => UserLogin::activeSessions()->distinct('user_id')->count('user_id'),
            'sessions_today' => UserLogin::today()->count(),
        ];

        $this->info("\n=== Current Session Stats ===");
        $this->info("Total sessions: {$stats['total_sessions']}");
        $this->info("Active sessions: {$stats['active_sessions']}");
        $this->info("Unique active users: {$stats['unique_active_users']}");
        $this->info("Sessions today: {$stats['sessions_today']}");
    }
}
