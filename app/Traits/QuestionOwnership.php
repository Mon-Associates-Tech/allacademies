<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait QuestionOwnership
{
    use LogsActivity;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->added_by = Auth::id();
            $model->modified_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->modified_by = Auth::id();
        });

        static::created(function ($model) {
            activity('question_management')
                ->performedOn($model)
                ->causedBy(Auth::user())
                ->withProperties([
                    'question_type' => $model->getQuestionType(),
                    'difficulty_level' => $model->difficulty_level ?? null,
                    'score' => $model->score ?? null,
                    'academic_subtopic_id' => $model->academic_subtopic_id ?? null,
                    'academic_topic_id' => $model->academic_topic_id ?? null,
                    'module' => 'questions',
                    'entity_type' => class_basename($model),
                    'metadata' => $model->getActivityMetadata(),
                ])
                ->log('created');
        });

        static::updated(function ($model) {
            $changes = $model->getChangesForLog();
            $dirtyFields = array_keys($model->getDirty());

            if (! empty($changes)) {
                // Get readable field names for the summary
                $changedFieldNames = [];
                foreach ($dirtyFields as $field) {
                    if (! in_array($field, ['updated_at', 'modified_by'])) {
                        $changedFieldNames[] = $model->getReadableFieldName($field);
                    }
                }

                activity('question_management')
                    ->performedOn($model)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'question_type' => $model->getQuestionType(),
                        'changes' => $changes,
                        'changed_fields' => $dirtyFields, // Add specific fields that changed
                        'changed_fields_readable' => $changedFieldNames, // Human-readable field names
                        'difficulty_level' => $model->difficulty_level ?? null,
                        'score' => $model->score ?? null,
                        'academic_subtopic_id' => $model->academic_subtopic_id ?? null,
                        'academic_topic_id' => $model->academic_topic_id ?? null,
                        'module' => 'questions',
                        'entity_type' => class_basename($model),
                        'metadata' => $model->getActivityMetadata(),
                        'change_summary' => 'Updated '.implode(', ', $changedFieldNames), // Summary of changes
                    ])
                    ->log('updated');
            }
        });

        static::deleted(function ($model) {
            activity('question_management')
                ->performedOn($model)
                ->causedBy(Auth::user())
                ->withProperties([
                    'question_type' => $model->getQuestionType(),
                    'question_data' => $model->getDataForDeletion(),
                    'module' => 'questions',
                    'entity_type' => class_basename($model),
                    'metadata' => $model->getActivityMetadata(),
                ])
                ->log('deleted');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('question_management')
            ->logOnly([
                'question', 'answer', 'score', 'difficulty_level',
                'academic_topic_id', 'academic_subtopic_id',
                'option_a', 'option_b', 'option_c', 'option_d', 'option_e',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => $eventName);
    }

    public function getQuestionType(): string
    {
        return match (class_basename($this)) {
            'EssayQuestion' => 'essay',
            'MultipleChoiceQuestion' => 'multiple_choice',
            'TrueOrFalseQuestion' => 'true_or_false',
            default => 'unknown'
        };
    }

    /**
     * Get changes formatted for activity logging
     */
    public function getChangesForLog(): array
    {
        $changes = [];
        $original = $this->getOriginal();

        foreach ($this->getDirty() as $key => $value) {
            if (! in_array($key, ['updated_at', 'modified_by'])) {
                $changes[$key] = [
                    'old' => $this->getOriginalValueForLog($key, $original[$key] ?? null),
                    'new' => $this->getNewValueForLog($key, $value),
                ];
            }
        }

        return $changes;
    }

    /**
     * Get metadata for activity logging
     */
    public function getActivityMetadata(): array
    {
        $metadata = [
            'model_id' => $this->id,
            'model_class' => get_class($this),
        ];

        // Add related model information if available
        if ($this->subtopic ?? null) {
            $metadata['subtopic_name'] = $this->subtopic->name ?? null;
        }

        if ($this->topic ?? null) {
            $metadata['topic_name'] = $this->topic->name ?? null;
        }

        return $metadata;
    }

    /**
     * Get data to store when model is deleted
     */
    public function getDataForDeletion(): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'score' => $this->score ?? null,
            'difficulty_level' => $this->difficulty_level ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Format original value for logging
     */
    protected function getOriginalValueForLog(string $key, $value)
    {
        // Handle special formatting for certain fields
        if ($key === 'question' || $key === 'answer') {
            return $value; // ? strip_tags($value) : null;
        }

        return $value;
    }

    /**
     * Format new value for logging
     */
    protected function getNewValueForLog(string $key, $value)
    {
        // Handle special formatting for certain fields
        if ($key === 'question' || $key === 'answer') {
            return $value ? strip_tags($value) : null;
        }

        return $value;
    }

    /**
     * Get readable field name for display
     */
    public function getReadableFieldName(string $field): string
    {
        $fieldNames = [
            'question' => 'Question Text',
            'answer' => 'Answer',
            'score' => 'Score',
            'difficulty_level' => 'Difficulty Level',
            'academic_topic_id' => 'Academic Topic',
            'academic_subtopic_id' => 'Academic Subtopic',
            'option_a' => 'Option A',
            'option_b' => 'Option B',
            'option_c' => 'Option C',
            'option_d' => 'Option D',
            'option_e' => 'Option E',
            'correct_option' => 'Correct Option',
            'explanation' => 'Explanation',
            'is_active' => 'Active Status',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date',
        ];

        return $fieldNames[$field] ?? ucwords(str_replace('_', ' ', $field));
    }

    // Define relationships
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
