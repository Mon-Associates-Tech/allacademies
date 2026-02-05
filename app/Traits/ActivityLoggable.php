<?php

namespace App\Traits;

use App\Models\UserActivity;
use App\Services\UserActivityService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * ActivityLoggable Trait
 *
 * Add this trait to any model to automatically log activities when it's created, updated, or deleted.
 * You can also manually log activities using the logActivity() method.
 *
 * Usage:
 *   use ActivityLoggable;
 *
 *   // In your model:
 *   public static function booted()
 *   {
 *       parent::booted();
 *       // Activities are logged automatically
 *   }
 *
 *   // Manual logging:
 *   $book->logActivity('favorite', 'Book Added to Reading List', 'library');
 *   $quiz->logActivity('submit', 'Quiz Completed', 'academic', ['score' => 95]);
 */
trait ActivityLoggable
{
    /**
     * Boot the trait
     */
    protected static function bootActivityLoggable(): void
    {
        static::created(fn(Model $model) => self::logModelActivity($model, 'create', 'created'));
        static::updated(fn(Model $model) => self::logModelActivity($model, 'update', 'updated'));
        static::deleted(fn(Model $model) => self::logModelActivity($model, 'delete', 'deleted'));
    }

    /**
     * Log a model activity automatically
     */
    protected static function logModelActivity(Model $model, string $type, string $action): void
    {
        // Skip logging if model is in a batch/transaction context where we don't want to log
        if (property_exists($model, 'skipActivityLog') && $model->skipActivityLog === true) {
            return;
        }

        // Get the activity category from the model if it defines it
        $category = $model->getActivityCategory() ?? 'content';
        $activityName = ucfirst($action) . ' ' . class_basename($model);

        // Get metadata from model if it provides it
        $metadata = [];
        if (method_exists($model, 'getActivityMetadata')) {
            $metadata = $model->getActivityMetadata();
        }

        // If this was an update, capture what changed
        if ($type === 'update' && method_exists($model, 'getChanges')) {
            $metadata['changes'] = $model->getChanges();
            $metadata['original'] = $model->getRawOriginal();
        }

        UserActivityService::log(
            $type,
            $activityName,
            $category,
            $model,
            $metadata
        );
    }

    /**
     * Log an activity manually for this model
     *
     * @param string $activityType - Type of activity (view, create, update, delete, download, upload, login, etc.)
     * @param string $activityName - Human-readable name
     * @param string $category - Category of activity
     * @param array $metadata - Additional metadata
     * @param string|null $description - Custom description
     */
    public function logActivity(
        string $activityType,
        string $activityName,
        string $category,
        array $metadata = [],
        ?string $description = null
    ): UserActivity {
        return UserActivityService::log(
            $activityType,
            $activityName,
            $category,
            $this,
            $metadata,
            $description
        );
    }

    /**
     * Log a custom activity with metadata
     * Shorthand for logActivity() with more customization
     */
    public function trackActivity(
        string $type,
        string $name,
        array $metadata = [],
        ?string $description = null
    ): UserActivity {
        return $this->logActivity($type, $name, $this->getActivityCategory() ?? 'content', $metadata, $description);
    }

    /**
     * Disable activity logging for the next operation
     * Usage: $model->skipActivityLogging()->update(['field' => 'value']);
     */
    public function skipActivityLogging(): self
    {
        $this->skipActivityLog = true;

        return $this;
    }

    /**
     * Get the activity category for this model
     * Override this method in your model to customize the category
     *
     * @return string|null
     */
    public function getActivityCategory(): ?string
    {
        $modelName = class_basename($this);

        return match ($modelName) {
            'Quiz', 'QuizSession', 'Assessment', 'Assignment', 'Lesson' => 'academic',
            'Book', 'BookSubscription', 'BookBorrowing', 'BookReadingProgress' => 'library',
            'Message', 'ChatMessage', 'AcademicChatMessage' => 'communication',
            'Payment', 'SchoolPayment' => 'payment',
            'User', 'UserPreference' => 'system',
            default => null,
        };
    }

    /**
     * Get additional metadata for activity logging
     * Override this method in your model to add custom metadata
     *
     * @return array
     */
    public function getActivityMetadata(): array
    {
        return [];
    }
}
