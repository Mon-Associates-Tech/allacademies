<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class ActivityLogHelper
{
    /**
     * Get activities for all academic models
     */
    public static function getAllAcademicActivities(int $limit = 50): Collection
    {
        $academicLogNames = [
            'academicgroup',
            'academiclevel',
            'academicsubject',
            'academictopic',
            'academicsubtopic'
        ];

        return Activity::whereIn('log_name', $academicLogNames)
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities for a specific model type
     */
    public static function getActivitiesForModel(string $modelType, int $limit = 50): Collection
    {
        $logName = strtolower($modelType);

        return Activity::where('log_name', $logName)
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by user
     */
    public static function getActivitiesByUser(int $userId, int $limit = 50): Collection
    {
        return Activity::where('causer_id', $userId)
            ->whereIn('log_name', [
                'academicgroup',
                'academiclevel',
                'academicsubject',
                'academictopic',
                'academicsubtopic'
            ])
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities within a date range
     */
    public static function getActivitiesByDateRange(
        Carbon $startDate,
        Carbon $endDate,
        int $limit = 100
    ): Collection {
        return Activity::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('log_name', [
                'academicgroup',
                'academiclevel',
                'academicsubject',
                'academictopic',
                'academicsubtopic'
            ])
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Format activities for display
     */
    public static function formatActivities(Collection $activities): array
    {
        return $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'description' => $activity->description,
                'action_type' => $activity->properties['action_type'] ?? $activity->log_name,
                'model_type' => ucfirst($activity->log_name),
                'model_id' => $activity->subject_id,
                'user_name' => $activity->properties['user_name'] ?? ($activity->causer?->name ?? 'System'),
                'user_id' => $activity->properties['user_id'] ?? $activity->causer_id,
                'changes' => [
                    'old' => $activity->properties['old'] ?? [],
                    'new' => $activity->properties['attributes'] ?? []
                ],
                'created_at' => $activity->created_at,
                'formatted_date' => $activity->created_at->format('M d, Y \a\t g:i A'),
                'time_ago' => $activity->created_at->diffForHumans(),
                'model_data' => $activity->subject ? [
                    'type' => class_basename($activity->subject),
                    'id' => $activity->subject->id,
                    'identifier' => static::getModelIdentifier($activity->subject)
                ] : null
            ];
        })->toArray();
    }

    /**
     * Get activity statistics
     */
    public static function getActivityStatistics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $query = Activity::whereIn('log_name', [
            'academicgroup',
            'academiclevel',
            'academicsubject',
            'academictopic',
            'academicsubtopic'
        ]);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $activities = $query->get();

        return [
            'total_activities' => $activities->count(),
            'activities_by_type' => $activities->groupBy('log_name')
                ->map->count()
                ->toArray(),
            'activities_by_action' => $activities->groupBy(function ($activity) {
                return $activity->properties['action_type'] ?? 'unknown';
            })->map->count()->toArray(),
            'activities_by_user' => $activities->groupBy('causer_id')
                ->map(function ($userActivities) {
                    $firstActivity = $userActivities->first();
                    return [
                        'count' => $userActivities->count(),
                        'user_name' => $firstActivity->properties['user_name'] ??
                            ($firstActivity->causer?->name ?? 'System')
                    ];
                })->toArray(),
            'recent_activity_count' => $activities->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Get a meaningful identifier for any model
     */
    private static function getModelIdentifier($model): string
    {
        if (!$model) return 'Unknown';

        $identifierFields = ['name', 'title', 'label', 'code', 'tag', 'slug'];

        foreach ($identifierFields as $field) {
            if (isset($model->$field) && !empty($model->$field)) {
                return $model->$field;
            }
        }

        return "ID: {$model->id}";
    }

    /**
     * Search activities by keyword
     */
    public static function searchActivities(string $keyword, int $limit = 50): Collection
    {
        return Activity::whereIn('log_name', [
            'academicgroup',
            'academiclevel',
            'academicsubject',
            'academictopic',
            'academicsubtopic'
        ])
            ->where(function ($query) use ($keyword) {
                $query->where('description', 'LIKE', "%{$keyword}%")
                    ->orWhere('properties->user_name', 'LIKE', "%{$keyword}%")
                    ->orWhereJsonContains('properties->attributes', $keyword)
                    ->orWhereJsonContains('properties->old', $keyword);
            })
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities for a specific model instance
     */
    public static function getModelActivity($model, int $limit = 50): array
    {
        if (!method_exists($model, 'activities')) {
            return [];
        }

        $activities = $model->activities()
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return static::formatActivities($activities);
    }
}
