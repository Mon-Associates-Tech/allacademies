<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'parameters',
        'messages',
        'analytics',
        'last_activity'
    ];

    protected $casts = [
        'parameters' => 'array',
        'messages' => 'array',
        'analytics' => 'array',
        'last_activity' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(AcademicChatAnalytics::class, 'session_id', 'session_id');
    }

    /**
     * Add a message to the session
     */
    public function addMessage(array $message): void
    {
        $messages = $this->messages ?? [];
        $messages[] = array_merge($message, [
            'timestamp' => now()->toISOString()
        ]);

        $this->update([
            'messages' => $messages,
            'last_activity' => now()
        ]);
    }

    /**
     * Get recent messages for context
     */
    public function getRecentMessages(int $limit = 10): array
    {
        $messages = $this->messages ?? [];
        return array_slice($messages, -$limit);
    }

    /**
     * Update session analytics
     */
    public function updateAnalytics(array $data): void
    {
        $analytics = $this->analytics ?? [];
        $analytics = array_merge($analytics, $data);

        $this->update(['analytics' => $analytics]);
    }

    /**
     * Check if session is expired
     */
    public function isExpired(): bool
    {
        $timeout = config('academic_chat.chat.session_timeout', 3600);
        return $this->last_activity->addSeconds($timeout)->isPast();
    }

    /**
     * Get session statistics
     */
    public function getStats(): array
    {
        $messages = $this->messages ?? [];
        $userMessages = array_filter($messages, fn($msg) => $msg['role'] === 'user');
        $aiMessages = array_filter($messages, fn($msg) => $msg['role'] === 'assistant');

        return [
            'total_messages' => count($messages),
            'user_messages' => count($userMessages),
            'ai_messages' => count($aiMessages),
            'session_duration' => $this->created_at->diffInMinutes($this->last_activity),
            'average_response_time' => $this->analytics['average_response_time'] ?? 0,
            'total_tokens_used' => $this->analytics['total_tokens_used'] ?? 0,
        ];
    }
}
