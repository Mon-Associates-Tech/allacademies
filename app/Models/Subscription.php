<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'package',
        'duration_in_months',
        'amount',
        'currency',
        'reference',
        'beneficiaries',
        'status',
        'expires_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => SubscriptionStatus::class,
        'expires_at' => 'datetime',
    ];

    public function subscriber()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function academicSubjects()
    {
        return $this->belongsToMany(AcademicSubject::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bookSubscriptions()
    {
        return $this->hasMany(BookSubscription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    /**
     * Get count of active students in this subscription's team/school
     */
    public function getActiveStudentCount(): int
    {
        $school = $this->team?->owner?->school;
        if (! $school) {
            return 0;
        }

        return Student::where('school_id', $school->id)->count();
    }

    /**
     * Check if subscription has capacity for additional students
     */
    public function hasCapacityFor(int $additionalStudents = 1): bool
    {
        return $this->getActiveStudentCount() + $additionalStudents <= $this->beneficiaries;
    }

    /**
     * Get remaining student capacity
     */
    public function getRemainingCapacity(): int
    {
        return max(0, $this->beneficiaries - $this->getActiveStudentCount());
    }

    /**
     * Check if subscription is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    /**
     * Scope: Get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    /**
     * Get the most recent active subscription for a team
     */
    public static function getActiveForTeam($teamId): ?self
    {
        return static::where('team_id', $teamId)
            ->active()
            ->latest('expires_at')
            ->first();
    }

    /**
     * Get capacity information as array
     */
    public function getCapacityInfo(): array
    {
        $activeCount = $this->getActiveStudentCount();
        $capacity = $this->beneficiaries;

        return [
            'total_capacity' => $capacity,
            'students_added' => $activeCount,
            'remaining_capacity' => $this->getRemainingCapacity(),
            'capacity_percentage' => round(($activeCount / $capacity) * 100),
            'is_at_capacity' => $activeCount >= $capacity,
            'days_until_expiry' => $this->expires_at->diffInDays(now()),
        ];
    }
}
