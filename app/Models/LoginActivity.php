<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LoginActivity extends Model
{
    protected $table = 'login_activities';

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
        'logout_type',
        'country'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'duration_minutes' => 'integer'
    ];

    // Scopes
    public function scopeActiveSessions(Builder $query)
    {
        return $query->whereNull('logout_at');
    }

    public function scopeCompletedSessions(Builder $query)
    {
        return $query->whereNotNull('logout_at');
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('login_at', today());
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Attributes
    protected function duration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->logout_at) {
                    // For completed sessions
                    if ($this->duration_minutes) {
                        return $this->formatMinutesToDuration($this->duration_minutes);
                    }

                    // Fallback calculation
                    if ($this->login_at && $this->logout_at) {
                        $minutes = $this->logout_at->diffInMinutes($this->login_at);
                        return $this->formatMinutesToDuration($minutes);
                    }

                    return 'Unknown';
                }

                // For active sessions
                if ($this->login_at) {
                    $minutes = now()->diffInMinutes($this->login_at);
                    return $this->formatMinutesToDuration($minutes) . ' (active)';
                }

                return 'Unknown';
            }
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->logout_at ? 'completed' : 'active'
        );
    }

    protected function logoutTypeDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->logout_type) {
                'manual' => 'Manual Logout',
                'session_timeout' => 'Session Timeout',
                'forced' => 'Forced Logout',
                'new_session' => 'New Login',
                'browser_close' => 'Browser Closed',
                default => 'Unknown'
            }
        );
    }

    // Helper methods
    private function formatMinutesToDuration(int $minutes): string
    {
        if ($minutes < 1) {
            return '< 1m';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return $remainingMinutes > 0
                ? "{$hours}h {$remainingMinutes}m"
                : "{$hours}h";
        }

        return "{$remainingMinutes}m";
    }
}
