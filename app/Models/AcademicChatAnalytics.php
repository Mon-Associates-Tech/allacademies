<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicChatAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'subject',
        'topics',
        'academic_level',
        'age',
        'learning_style',
        'message_count',
        'total_tokens_used',
        'average_response_time',
        'learning_outcomes',
    ];

    protected $casts = [
        'topics' => 'array',
        'learning_outcomes' => 'array',
        'average_response_time' => 'decimal:2',
    ];

    /**
     * Record a new interaction
     */
    public static function recordInteraction(array $data): void
    {
        $analytics = static::firstOrCreate(
            ['session_id' => $data['session_id']],
            $data
        );

        $analytics->increment('message_count');

        if (isset($data['tokens_used'])) {
            $analytics->increment('total_tokens_used', $data['tokens_used']);
        }

        if (isset($data['response_time'])) {
            $currentAvg = $analytics->average_response_time ?? 0;
            $newAvg = (($currentAvg * ($analytics->message_count - 1)) + $data['response_time']) / $analytics->message_count;
            $analytics->update(['average_response_time' => $newAvg]);
        }
    }

    /**
     * Get learning insights for a user
     */
    public static function getLearningInsights(int $userId): array
    {
        $analytics = static::where('user_id', $userId)->get();

        if ($analytics->isEmpty()) {
            return [];
        }

        $subjects = $analytics->pluck('subject')->filter()->unique()->values();
        $totalMessages = $analytics->sum('message_count');
        $totalTokens = $analytics->sum('total_tokens_used');
        $avgResponseTime = $analytics->avg('average_response_time');

        $topTopics = $analytics->pluck('topics')
            ->flatten()
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5);

        return [
            'subjects_studied' => $subjects,
            'total_interactions' => $totalMessages,
            'total_tokens_used' => $totalTokens,
            'average_response_time' => round($avgResponseTime, 2),
            'top_topics' => $topTopics,
            'learning_sessions' => $analytics->count(),
            'most_active_subject' => $subjects->first(),
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicChatSession::class, 'session_id', 'session_id');
    }
}
