<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait AcademicGroupLogs
{
    use LogsActivity;

    protected static function bootAcademicGroupLogs()
    {
        // Log when model is created
        static::created(function ($model) {
            $user = Auth::user();

            activity()
                ->causedBy($user)
                ->performedOn($model)
                ->withProperties([
                    'attributes' => $model->getAttributes(),
                    'old' => [],
                    'user_name' => $user ? $user->name : 'System',
                    'user_id' => $user ? $user->id : null,
                    'action_type' => 'created',
                ])
                ->log('created');
        });

        // Log when model is updated
        static::updated(function ($model) {
            $user = Auth::user();
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            // Only log if there are actual changes
            if (! empty($changes)) {
                $oldValues = [];
                foreach ($changes as $key => $newValue) {
                    $oldValues[$key] = $original[$key] ?? null;
                }

                activity()
                    ->causedBy($user)
                    ->performedOn($model)
                    ->withProperties([
                        'attributes' => $changes,
                        'old' => $oldValues,
                        'user_name' => $user ? $user->name : 'System',
                        'user_id' => $user ? $user->id : null,
                        'action_type' => 'updated',
                    ])
                    ->log('updated');
            }
        });

        // Log when model is deleted (soft delete)
        static::deleted(function ($model) {
            $user = Auth::user();

            activity()
                ->causedBy($user)
                ->performedOn($model)
                ->withProperties([
                    'attributes' => $model->getAttributes(),
                    'old' => $model->getAttributes(),
                    'user_name' => $user ? $user->name : 'System',
                    'user_id' => $user ? $user->id : null,
                    'action_type' => 'deleted',
                ])
                ->log('deleted');
        });

        // Log when model is restored (if using soft deletes)
        /*        static::restored(function ($model) {
                    $user = Auth::user();

                    activity()
                        ->causedBy($user)
                        ->performedOn($model)
                        ->withProperties([
                            'attributes' => $model->getAttributes(),
                            'old' => [],
                            'user_name' => $user ? $user->name : 'System',
                            'user_id' => $user ? $user->id : null,
                            'action_type' => 'restored'
                        ])
                        ->log('restored');
                });*/
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable()) // Log only fillable attributes
            ->logOnlyDirty() // Only log changed attributes
            ->dontSubmitEmptyLogs() // Don't log if no changes
            ->useLogName($this->getLogName()); // Dynamic log name based on model
    }

    /**
     * Get the log name for this model
     */
    protected function getLogName(): string
    {
        return strtolower(class_basename($this));
    }

    /**
     * Get the description for the activity log
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = class_basename($this);
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';

        // Try to get a meaningful identifier for the model
        $identifier = $this->getModelIdentifier();

        return match ($eventName) {
            'created' => "{$userName} created {$modelName} '{$identifier}'",
            'updated' => "{$userName} updated {$modelName} '{$identifier}'",
            'deleted' => "{$userName} deleted {$modelName} '{$identifier}'",
            'restored' => "{$userName} restored {$modelName} '{$identifier}'",
            default => "{$userName} {$eventName} {$modelName} '{$identifier}'"
        };
    }

    /**
     * Get a meaningful identifier for the model
     */
    protected function getModelIdentifier(): string
    {
        // Try common identifier fields in order of preference
        $identifierFields = ['name', 'title', 'label', 'code', 'tag', 'slug'];

        foreach ($identifierFields as $field) {
            if (isset($this->attributes[$field]) && ! empty($this->attributes[$field])) {
                return $this->attributes[$field];
            }
        }

        // Fallback to ID if no meaningful identifier found
        return "ID: {$this->id}";
    }

    /**
     * Get activities for this model instance
     */
    public function activities()
    {
        return $this->morphMany(config('activitylog.activity_model'), 'subject');
    }

    /**
     * Get formatted activity history
     */
    public function getActivityHistory()
    {
        return $this->activities()
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'action_type' => $activity->properties['action_type'] ?? $activity->log_name,
                    'user_name' => $activity->properties['user_name'] ?? ($activity->causer ? $activity->causer->name : 'System'),
                    'user_id' => $activity->properties['user_id'] ?? ($activity->causer ? $activity->causer->id : null),
                    'changes' => [
                        'old' => $activity->properties['old'] ?? [],
                        'new' => $activity->properties['attributes'] ?? [],
                    ],
                    'created_at' => $activity->created_at,
                    'formatted_date' => $activity->created_at->format('M d, Y \a\t g:i A'),
                ];
            });
    }
}
