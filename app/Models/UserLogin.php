<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class UserLogin extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'location',
        'session_id',
        'login_at',
        'logout_at',
        'duration_minutes',
        'logout_type'
    ];

    protected $table = 'login_activities';
    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'duration_minutes' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for active sessions
    public function scopeActiveSessions(Builder $query)
    {
        return $query->whereNull('logout_at');
    }

    // Scope for completed sessions
    public function scopeCompletedSessions(Builder $query)
    {
        return $query->whereNotNull('logout_at');
    }

    // Scope for current day
    public function scopeToday(Builder $query)
    {
        return $query->whereDate('login_at', today());
    }

    // Get formatted duration
    public function getFormattedDurationAttribute()
    {
        // If session is completed (has logout_at), show the stored duration
        if ($this->logout_at) {
            if ($this->duration_minutes && $this->duration_minutes > 0) {
                return $this->formatMinutesToDuration($this->duration_minutes);
            } else {
                // Fallback: calculate duration if not stored
                if ($this->login_at && $this->logout_at) {
                    $calculatedMinutes = $this->logout_at->diffInMinutes($this->login_at);
                    return $this->formatMinutesToDuration($calculatedMinutes);
                }
                return 'Unknown';
            }
        }

        // If session is active (no logout_at), show current session duration
        if (!$this->logout_at && $this->login_at) {
            $currentDuration = now()->diffInMinutes($this->login_at);
            return $this->formatMinutesToDuration($currentDuration) . ' (active)';
        }

        return 'Unknown';
    }

    // Helper method to format minutes to duration string
    private function formatMinutesToDuration($minutes)
    {
        if ($minutes < 1) {
            return '< 1m';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            if ($remainingMinutes > 0) {
                return "{$hours}h {$remainingMinutes}m";
            }
            return "{$hours}h";
        }

        return "{$remainingMinutes}m";
    }

    // Helper method to format time ago
    private function formatTimeAgo($loginTime)
    {
        if (!$loginTime) {
            return 'Unknown';
        }

        $now = now();
        $minutesAgo = $now->diffInMinutes($loginTime);

        if ($minutesAgo < 1) {
            return 'Just now';
        } elseif ($minutesAgo < 60) {
            return $minutesAgo . ' minute' . ($minutesAgo > 1 ? 's' : '') . ' ago';
        } else {
            $hoursAgo = floor($minutesAgo / 60);
            $remainingMinutes = $minutesAgo % 60;

            $timeString = $hoursAgo . ' hour' . ($hoursAgo > 1 ? 's' : '');
            if ($remainingMinutes > 0) {
                $timeString .= ' ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
            }
            return $timeString . ' ago';
        }
    }

    // Alternative method for getting current session duration (useful for real-time updates)
    public function getCurrentDurationAttribute()
    {
        if (!$this->login_at) {
            return 0;
        }

        $endTime = $this->logout_at ?: now();
        return $this->login_at->diffInMinutes($endTime);
    }

    // Get session status
    public function getStatusAttribute()
    {
        return $this->logout_at ? 'completed' : 'active';
    }

    // Get logout type display name
    public function getLogoutTypeDisplayAttribute()
    {
        return match($this->logout_type) {
            'manual' => 'Manual Logout',
            'session_timeout' => 'Session Timeout',
            'forced' => 'Forced Logout',
            'browser_close' => 'Browser Closed',
            default => 'Unknown'
        };
    }

    // Get human readable time difference for login
    public function getLoginTimeAttribute()
    {
        if (!$this->login_at) {
            return 'Unknown';
        }

        return $this->login_at->diffForHumans();
    }

    // Get human readable time difference for logout
    public function getLogoutTimeAttribute()
    {
        if (!$this->logout_at) {
            return null;
        }

        return $this->logout_at->diffForHumans();
    }
}
