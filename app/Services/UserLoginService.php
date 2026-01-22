<?php

namespace App\Services;

use App\Models\LoginActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class UserLoginService
{
    private Agent $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function handleLogin($user): LoginActivity
    {
        // Close any existing active sessions for this user first
        LoginActivity::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->where('session_id', '!=', session()->getId())
            ->update([
                'logout_at' => now(),
                'action' => 'logged_out',
                'logout_type' => 'new_session',
                'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, login_at, NOW())')
            ]);

        // Update user online status
        $user->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        // Set cache for 5 minutes
        Cache::put('user-online-'.$user->id, true, now()->addMinutes(5));

        // Get location data
        $ip = request()->ip();
        $location = Location::get($ip);

        // Create new login session
        return LoginActivity::create([
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'user_agent' => request()->userAgent(),
            'ip_address' => $ip,
            'device_type' => $this->agent->device() ?: 'Unknown',
            'platform' => $this->agent->platform(),
            'browser' => $this->agent->browser(),
            'action' => 'logged_in',
            'country' => $location->countryName ?? 'Unknown',
            'location' => $location->cityName ?? ($ip === '127.0.0.1' || $ip === '::1' ? 'Local' : null),
            'login_at' => now(),
        ]);
    }

    public function handleLogout($user, string $logoutType = 'manual'): void
    {
        if (!$user) {
            return;
        }

        // Find active session for this user/session
        $activeSession = LoginActivity::where('user_id', $user->id)
            ->where('session_id', session()->getId())
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($activeSession) {
            $logoutTime = now();
            $duration = $logoutTime->diffInMinutes($activeSession->login_at);

            $activeSession->update([
                'action' => 'logged_out',
                'logout_at' => $logoutTime,
                'duration_minutes' => max(0, $duration),
                'logout_type' => $logoutType
            ]);

            \Log::info('User logout handled', [
                'user_id' => $user->id,
                'session_id' => $activeSession->id,
                'login_at' => $activeSession->login_at,
                'logout_at' => $logoutTime,
                'duration_minutes' => $duration,
                'logout_type' => $logoutType,
            ]);
        }

        // Update user online status
        $user->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);

        // Remove from cache
        Cache::forget('user-online-'.$user->id);
    }

    public function handleSessionTimeout($userId): void
    {
        $user = \App\Models\User::find($userId);
        if (! $user) {
            return;
        }

        $activeSessions = LoginActivity::where('user_id', $userId)
            ->whereNull('logout_at')
            ->get();

        foreach ($activeSessions as $session) {
            $logoutTime = now();
            $duration = $logoutTime->diffInMinutes($session->login_at);

            $session->update([
                'action' => 'logged_out',
                'logout_at' => $logoutTime,
                'duration_minutes' => max(0, $duration),
                'logout_type' => 'session_timeout'
            ]);
        }

        $user->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);

        Cache::forget('user-online-' . $userId);
    }

    public function forceLogoutSpecificSession($sessionId): bool
    {
        $session = LoginActivity::find($sessionId);
        if (!$session || $session->logout_at) {
            return false;
        }

        $logoutTime = now();
        $duration = $logoutTime->diffInMinutes($session->login_at);

        $session->update([
            'action' => 'logged_out',
            'logout_at' => $logoutTime,
            'duration_minutes' => max(0, $duration),
            'logout_type' => 'forced',
        ]);

        $remainingActiveSessions = LoginActivity::where('user_id', $session->user_id)
            ->whereNull('logout_at')
            ->count();

        if ($remainingActiveSessions === 0) {
            $session->user->update([
                'is_online' => false,
                'last_seen_at' => now(),
            ]);
        }

        Cache::forget('user-online-' . $session->user_id);
        $this->invalidateSpecificSession($session->session_id, $session->user_id);

        return true;
    }

    public function getActiveUserCount(): int
    {
        $sessionDriver = config('session.driver');

        if ($sessionDriver === 'database') {
            return LoginActivity::activeSessions()
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('sessions')
                        ->whereColumn('sessions.id', 'login_activities.session_id');
                })
                ->distinct('user_id')
                ->count('user_id');
        }

        return LoginActivity::activeSessions()
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getUserSessionStats($userId): array
    {
        $sessions = LoginActivity::where('user_id', $userId)
            ->completedSessions()
            ->get();

        $totalSessions = $sessions->count();
        $totalDuration = $sessions->sum('duration_minutes');
        $averageDuration = $totalSessions > 0 ? round($totalDuration / $totalSessions) : 0;

        return [
            'total_sessions' => $totalSessions,
            'total_duration_minutes' => $totalDuration,
            'average_duration_minutes' => $averageDuration,
            'active_sessions' => LoginActivity::where('user_id', $userId)->activeSessions()->count()
        ];
    }

    private function invalidateSpecificSession($sessionId, $userId): void
    {
        try {
            $sessionDriver = config('session.driver');

            switch ($sessionDriver) {
                case 'database':
                    DB::table('sessions')->where('id', $sessionId)->delete();
                    break;

                case 'file':
                    $sessionPath = storage_path('framework/sessions/sess_' . $sessionId);
                    if (file_exists($sessionPath)) {
                        unlink($sessionPath);
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to invalidate session', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
