<?php

namespace App\Traits;

use App\Models\UserActivity;
use App\Services\UserActivityService;
use Illuminate\Database\Eloquent\Model;

/**
 * ActivityLoggable Trait
 *
 * Add this trait to any model to automatically log activities when it's created, updated, or deleted.
 *
 * ⚠️ IMPORTANT: Livewire Component Updates
 * ========================================
 * This trait automatically skips logging for Livewire update requests (POST /livewire/update)
 * because they lack meaningful context. Instead, Livewire component methods should use
 * explicit logging for better control and context.
 *
 * INSTEAD OF:
 *   // Auto-logged (but will be skipped)
 *   $teacher->update(['name' => 'John']);
 *
 * DO THIS:
 *   // Explicit logging with context
 *   $teacher->update(['name' => 'John']);
 *   $teacher->logActivity('update', 'Teacher Name Updated', 'academic',
 *       ['changed_field' => 'name', 'new_value' => 'John']
 *   );
 *
 * Automatic Logging Applies To:
 * - Regular HTTP POST/PUT requests (form submissions)
 * - API endpoints
 * - Console commands (via CLI)
 * - NOT Livewire component methods (use explicit logging)
 *
 * Sensitive Fields:
 * By default, sensitive fields like 'password', 'api_key', 'secret', etc. are excluded from logs.
 * To add model-specific sensitive fields, override getSensitiveFieldsForLogging():
 *
 *   public function getSensitiveFieldsForLogging(): array
 *   {
 *       return ['ssn', 'medical_record'];
 *   }
 *
 * Usage:
 *   use ActivityLoggable;
 *
 *   // Manual logging (recommended for Livewire):
 *   $book->logActivity('favorite', 'Book Added to Reading List', 'library');
 *   $quiz->logActivity('submit', 'Quiz Completed', 'academic', ['score' => 95]);
 *
 *   // Skip logging for bulk operations:
 *   $model->skipActivityLogging()->update(['field' => 'value']);
 */
trait ActivityLoggable
{
    /**
     * Boot the trait
     */
    protected static function bootActivityLoggable(): void
    {
        static::created(fn (Model $model) => self::logModelActivity($model, 'create', 'created'));
        static::updated(fn (Model $model) => self::logModelActivity($model, 'update', 'updated'));
        static::deleted(fn (Model $model) => self::logModelActivity($model, 'delete', 'deleted'));
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

        // Skip logging for Livewire update requests - they should use explicit logging
        // Livewire updates are sent through POST livewire/update which creates unnecessary noise
        if (self::isLivewireRequest()) {
            return;
        }

        // Get the activity category from the model if it defines it
        $category = $model->getActivityCategory() ?? 'content';
        $activityName = ucfirst($action).' '.class_basename($model);

        // Get metadata from model if it provides it
        $metadata = [];
        if (method_exists($model, 'getActivityMetadata')) {
            $metadata = $model->getActivityMetadata();
        }

        // If this was an update, capture what changed (but filter sensitive fields)
        if ($type === 'update' && method_exists($model, 'getChanges')) {
            $changes = $model->getChanges();
            $original = $model->getRawOriginal();

            // Filter out sensitive fields
            $sensitiveFields = self::getSensitiveFields($model);
            foreach ($sensitiveFields as $field) {
                unset($changes[$field], $original[$field]);
            }

            if (! empty($changes)) {
                $metadata['changes'] = $changes;
                $metadata['original'] = $original;
            }
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
     * Check if the current request is from Livewire
     * Livewire requests should use explicit logging, not automatic logging
     */
    protected static function isLivewireRequest(): bool
    {
        if (! class_exists('\Illuminate\Support\Facades\Request')) {
            return false;
        }

        $request = \Illuminate\Support\Facades\Request::instance();

        // Check if this is a Livewire request
        if ($request->header('X-Livewire') === 'true') {
            return true;
        }

        // Check if the request path indicates a Livewire operation
        $path = $request->path();
        $livewirePaths = [
            'livewire/update',
            'livewire/message',
            'livewire/call',
        ];

        foreach ($livewirePaths as $livewirePath) {
            if (str_contains($path, $livewirePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get list of sensitive fields that should not be logged
     * Models can override getSensitiveFieldsForLogging() to customize
     */
    protected static function getSensitiveFields(Model $model): array
    {
        // Default sensitive fields
        $defaultSensitive = [
            'password',
            'api_key',
            'api_token',
            'secret',
            'access_token',
            'refresh_token',
            'ssn',
            'social_security_number',
            'credit_card',
            'card_number',
            'bank_account',
            'bank_routing',
            'encrypted_password',
            'hashed_password',
            'remember_token',
            'two_factor_code',
            '2fa_code',
            'recovery_code',
            'recovery_codes',
            'backup_codes',
            'verification_code',
            'otp',
            'oauth_token',
            'stripe_token',
            'paypal_token',
            'private_key',
            'pgp_key',
            'jwt_token',
        ];

        // Allow models to extend the sensitive fields list
        if (method_exists($model, 'getSensitiveFieldsForLogging')) {
            $defaultSensitive = array_merge($defaultSensitive, $model->getSensitiveFieldsForLogging());
        }

        return $defaultSensitive;
    }

    /**
     * Log an activity manually for this model
     *
     * @param  string  $activityType  - Type of activity (view, create, update, delete, download, upload, login, etc.)
     * @param  string  $activityName  - Human-readable name
     * @param  string  $category  - Category of activity
     * @param  array  $metadata  - Additional metadata
     * @param  string|null  $description  - Custom description
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
     */
    public function getActivityMetadata(): array
    {
        return [];
    }
}
