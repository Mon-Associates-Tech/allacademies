<?php

namespace App\Services;

use App\Models\UserLogin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;

class UserLoginService
{
    private Agent $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function handleLogin($user): UserLogin
    {
        // Close any existing active sessions for this user/session
        $this->closeExistingSessions($user);

        // Update user online status
        $user->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        // Set cache for 5 minutes
        Cache::put('user-online-' . $user->id, true, now()->addMinutes(5));

        // Create new login session
        return UserLogin::create([
            'user_id' => $user->id,
            'action' => 'logged_in',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $this->agent->device() ?: 'Unknown',
            'browser' => $this->agent->browser(),
            'platform' => $this->agent->platform(),
            'location' => $this->getLocationFromIP(request()->ip()),
            'session_id' => session()->getId(),
            'login_at' => now(),
        ]);
    }

    public function handleLogout($user, string $logoutType = 'manual'): void
    {
        // Find active session for this user/session
        $activeSession = UserLogin::where('user_id', $user->id)
            ->where('session_id', session()->getId())
            ->whereNull('logout_at')
            ->first();

        if ($activeSession) {
            $logoutTime = now();
            $duration = $logoutTime->diffInMinutes($activeSession->login_at);

            // Ensure duration is at least 0
            $duration = max(0, $duration);

            $activeSession->update([
                'action' => 'logged_out',
                'logout_at' => $logoutTime,
                'duration_minutes' => $duration,
                'logout_type' => $logoutType
            ]);

            // Debug logging
            \Log::info('User logout handled', [
                'user_id' => $user->id,
                'session_id' => $activeSession->id,
                'login_at' => $activeSession->login_at,
                'logout_at' => $logoutTime,
                'duration_minutes' => $duration,
                'logout_type' => $logoutType
            ]);
        }

        // Update user online status
        $user->update([
            'is_online' => false,
            'last_seen_at' => now()
        ]);

        // Remove from cache
        Cache::forget('user-online-' . $user->id);
    }

    public function handleSessionTimeout($userId): void
    {
        $user = \App\Models\User::find($userId);
        if (!$user) return;

        // Find and close all active sessions for this user
        $activeSessions = UserLogin::where('user_id', $userId)
            ->whereNull('logout_at')
            ->get();

        foreach ($activeSessions as $session) {
            $logoutTime = now();
            $duration = $logoutTime->diffInMinutes($session->login_at);

            $session->update([
                'action' => 'logged_out',
                'logout_at' => $logoutTime,
                'duration_minutes' => max(0, $duration), // Ensure non-negative
                'logout_type' => 'session_timeout'
            ]);

            // Debug logging
            \Log::info('Session timeout handled', [
                'user_id' => $userId,
                'session_id' => $session->id,
                'login_at' => $session->login_at,
                'logout_at' => $logoutTime,
                'duration_minutes' => $duration
            ]);
        }

        // Update user status
        $user->update([
            'is_online' => false,
            'last_seen_at' => now()
        ]);

        Cache::forget('user-online-' . $userId);

        // Invalidate actual sessions
        $this->invalidateAllUserSessions($userId);
    }

    public function forceLogoutSpecificSession($sessionId): bool
    {
        $session = UserLogin::find($sessionId);
        if (!$session || $session->logout_at) {
            return false;
        }

        $logoutTime = now();
        $duration = $logoutTime->diffInMinutes($session->login_at);

        // Update the specific session
        $session->update([
            'action' => 'logged_out',
            'logout_at' => $logoutTime,
            'duration_minutes' => max(0, $duration),
            'logout_type' => 'forced'
        ]);

        // Update user status if this was their last active session
        $remainingActiveSessions = UserLogin::where('user_id', $session->user_id)
            ->whereNull('logout_at')
            ->count();

        if ($remainingActiveSessions === 0) {
            $session->user->update([
                'is_online' => false,
                'last_seen_at' => now()
            ]);
        }

        Cache::forget('user-online-' . $session->user_id);

        // Invalidate the actual browser session
        $this->invalidateSpecificSession($session->session_id, $session->user_id);

        return true;
    }

    private function invalidateSpecificSession($sessionId, $userId)
    {
        try {
            $sessionDriver = config('session.driver');

            switch ($sessionDriver) {
                case 'database':
                    DB::table('sessions')
                        ->where('id', $sessionId)
                        ->delete();
                    break;

                case 'file':
                    $sessionPath = storage_path('framework/sessions/sess_' . $sessionId);
                    if (file_exists($sessionPath)) {
                        unlink($sessionPath);
                    }
                    break;

                case 'redis':
                    $redis = app('redis');
                    $key = config('session.cookie') . ':' . $sessionId;
                    $redis->del($key);
                    break;
            }

        } catch (\Exception $e) {
            \Log::error('Failed to invalidate specific session', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function invalidateAllUserSessions($userId)
    {
        try {
            $sessionDriver = config('session.driver');

            switch ($sessionDriver) {
                case 'database':
                    DB::table('sessions')
                        ->where('user_id', $userId)
                        ->delete();
                    break;

                case 'file':
                    // For file sessions, we need to find sessions by content
                    $sessionPath = storage_path('framework/sessions');
                    if (is_dir($sessionPath)) {
                        $files = glob($sessionPath . '/sess_*');
                        foreach ($files as $file) {
                            $content = file_get_contents($file);
                            if (strpos($content, 'login_web_' . sha1('web') . '";i:' . $userId . ';') !== false) {
                                unlink($file);
                            }
                        }
                    }
                break;

                case 'redis':
                    // This is more complex for Redis, would need to scan all keys
                    break;
            }

            // Also clear remember token
            DB::table('users')
                ->where('id', $userId)
                ->update(['remember_token' => null]);

        } catch (\Exception $e) {
            \Log::error('Failed to invalidate all user sessions', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updateUserActivity($user): void
    {
        // Update last seen timestamp
        $user->update(['last_seen_at' => now()]);

        // Extend cache
        Cache::put('user-online-' . $user->id, true, now()->addMinutes(5));
    }

    private function closeExistingSessions($user): void
    {
        // Close any existing active sessions for this user
        $activeSessions = UserLogin::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->get();

        foreach ($activeSessions as $session) {
            $logoutTime = now();
            $duration = $logoutTime->diffInMinutes($session->login_at);

            // Ensure duration is at least 0
            $duration = max(0, $duration);

            $session->update([
                'action' => 'logged_out',
                'logout_at' => $logoutTime,
                'duration_minutes' => $duration,
                'logout_type' => 'browser_close'
            ]);
        }
    }

    private function getLocationFromIP(string $ip): ?string
    {
        // Simple location detection - you can integrate with services like GeoIP
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }

        // You can integrate with GeoIP services here
        return null;
    }


public function getActiveUserCount(): int
{
    // Only count users with valid active sessions
    $sessionDriver = config('session.driver');

    switch ($sessionDriver) {
        case 'database':
            // Count users who have both UserLogin active sessions AND Laravel sessions
            return UserLogin::activeSessions()
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('sessions')
                        ->whereColumn('sessions.id', 'user_logins.session_id');
                })
                ->distinct('user_id')
                ->count('user_id');

        case 'file':
            // For file sessions, we need to check if session files exist
            $activeSessions = UserLogin::activeSessions()->get();
            $validUserIds = [];
            $sessionPath = storage_path('framework/sessions');

            foreach ($activeSessions as $session) {
                $sessionFile = $sessionPath . '/sess_' . $session->session_id;
                if (file_exists($sessionFile)) {
                    $validUserIds[] = $session->user_id;
                }
            }

            return count(array_unique($validUserIds));

        default:
            // Fallback to simple count
            return UserLogin::activeSessions()
                ->distinct('user_id')
                ->count('user_id');
    }
}

public function getRealTimeActiveUserCount(): int
{
    // Since we're not using Redis, we'll count users who have been active recently
    // based on their last_seen_at timestamp and cache entries

    $activeUserIds = [];

    // Method 1: Check users who have been seen in the last 5 minutes
    $recentlyActiveUsers = \App\Models\User::where('is_online', true)
        ->where('last_seen_at', '>=', now()->subMinutes(5))
        ->pluck('id')
        ->toArray();

    // Method 2: Also check cache entries (for users who might have just logged in)
    // We'll try to check if cache keys exist for each potentially active user
    foreach ($recentlyActiveUsers as $userId) {
        if (Cache::has('user-online-' . $userId)) {
            $activeUserIds[] = $userId;
        }
    }

    // If no cache hits, fall back to database-only check
    if (empty($activeUserIds)) {
        return count($recentlyActiveUsers);
    }

    return count(array_unique($activeUserIds));
}

    public function getUserSessionStats($userId): array
    {
        $sessions = UserLogin::where('user_id', $userId)
            ->completedSessions()
            ->get();

        $totalSessions = $sessions->count();
        $totalDuration = $sessions->sum('duration_minutes');
        $averageDuration = $totalSessions > 0 ? round($totalDuration / $totalSessions) : 0;

        return [
            'total_sessions' => $totalSessions,
            'total_duration_minutes' => $totalDuration,
            'average_duration_minutes' => $averageDuration,
            'active_sessions' => UserLogin::where('user_id', $userId)->activeSessions()->count()
        ];
    }
}
