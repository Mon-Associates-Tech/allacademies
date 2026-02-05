<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_name',
        'description',
        'category',
        'subject_type',
        'subject_id',
        'metadata',
        'ip_address',
        'user_agent',
        'reference_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Belongs to User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related subject
     */
    public function subject()
    {
        return $this->morphTo('subject');
    }

    /**
     * Scope: Filter by activity type
     */
    public function scopeByActivityType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Recent activities
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get category label attribute
     */
    protected function categoryLabel(): Attribute
    {
        return new Attribute(
            get: fn() => match ($this->category) {
                'authentication' => 'Authentication',
                'academic' => 'Academic',
                'library' => 'Library',
                'communication' => 'Communication',
                'payment' => 'Payment',
                'system' => 'System',
                'document' => 'Document',
                'content' => 'Content',
                'settings' => 'Settings',
                'assignment' => 'Assignment',
                default => ucfirst($this->category),
            }
        );
    }

    /**
     * Get activity type label attribute
     */
    protected function activityTypeLabel(): Attribute
    {
        return new Attribute(
            get: fn() => match ($this->activity_type) {
                'view' => 'Viewed',
                'create' => 'Created',
                'update' => 'Updated',
                'delete' => 'Deleted',
                'download' => 'Downloaded',
                'upload' => 'Uploaded',
                'publish' => 'Published',
                'subscribe' => 'Subscribed',
                'unsubscribe' => 'Unsubscribed',
                'purchase' => 'Purchased',
                'login' => 'Logged In',
                'logout' => 'Logged Out',
                'read' => 'Read',
                'comment' => 'Commented',
                'reply' => 'Replied',
                'share' => 'Shared',
                'favorite' => 'Favorited',
                'unfavorite' => 'Unfavorited',
                'submit' => 'Submitted',
                'start' => 'Started',
                'complete' => 'Completed',
                'cancel' => 'Cancelled',
                'approve' => 'Approved',
                'reject' => 'Rejected',
                'export' => 'Exported',
                default => ucfirst(str_replace('_', ' ', $this->activity_type)),
            }
        );
    }
}
