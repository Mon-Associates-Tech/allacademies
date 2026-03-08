<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserActivityService
{
    /**
     * Log a user activity
     *
     * @param string $activityType - Type of activity (view, create, update, delete, download, upload, login, logout, etc.)
     * @param string $activityName - Human-readable name of the activity
     * @param string $category - Category of the activity
     * @param Model|null $subject - The model that was interacted with
     * @param array $metadata - Additional metadata about the activity
     * @param string|null $description - Custom description
     * @param int|null $referenceId - Additional reference ID
     * @param int|null $userId - Override user ID (if null, uses authenticated user)
     */
    public static function log(
        string $activityType,
        string $activityName,
        string $category,
        ?Model $subject = null,
        array $metadata = [],
        ?string $description = null,
        ?int $referenceId = null,
        ?int $userId = null
    ): UserActivity {
        $userId ??= Auth::id();

        if (! $userId) {
            return new UserActivity(); // Return empty if no user
        }

        $activity = new UserActivity([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'activity_name' => $activityName,
            'category' => $category,
            'description' => $description ?? self::generateDescription($activityType, $activityName),
            'reference_id' => $referenceId,
            'metadata' => self::enrichMetadata($metadata),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        if ($subject) {
            $activity->subject()->associate($subject);
        }

        $activity->save();

        return $activity;
    }

    /**
     * Log multiple activities at once
     */
    public static function logMany(array $activities): array
    {
        return array_map(fn(array $activityData) => self::log(
            $activityData['activity_type'],
            $activityData['activity_name'],
            $activityData['category'],
            $activityData['subject'] ?? null,
            $activityData['metadata'] ?? [],
            $activityData['description'] ?? null,
            $activityData['reference_id'] ?? null,
            $activityData['user_id'] ?? null,
        ), $activities);
    }

    /**
     * Common activity logging methods
     */

    public static function logLogin(User $user): UserActivity
    {
        return self::log(
            'login',
            'User Login',
            'authentication',
            $user,
            ['login_time' => now()->toIso8601String()],
            description: "{$user->name} logged in"
        );
    }

    public static function logLogout(User $user): UserActivity
    {
        return self::log(
            'logout',
            'User Logout',
            'authentication',
            $user,
            ['logout_time' => now()->toIso8601String()],
            description: "{$user->name} logged out"
        );
    }

    public static function logQuizStart(User $user, Model $quiz, array $metadata = []): UserActivity
    {
        $quizTitle = $quiz->title ?? 'Untitled';

        return self::log(
            'start',
            'Quiz Started',
            'academic',
            $quiz,
            array_merge(['started_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} started quiz: {$quizTitle}"
        );
    }

    public static function logQuizSubmit(User $user, Model $quiz, array $metadata = []): UserActivity
    {
        $quizTitle = $quiz->title ?? 'Untitled';

        return self::log(
            'submit',
            'Quiz Submitted',
            'academic',
            $quiz,
            array_merge(['submitted_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} submitted quiz: {$quizTitle}"
        );
    }

    public static function logDocumentUpload(User $user, Model $document, array $metadata = []): UserActivity
    {
        $documentName = $document->title ?? $document->name ?? 'Untitled';

        return self::log(
            'upload',
            'Document Uploaded',
            'document',
            $document,
            array_merge(['uploaded_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} uploaded document: {$documentName}"
        );
    }

    public static function logDocumentDownload(User $user, Model $document, array $metadata = []): UserActivity
    {
        $documentName = $document->title ?? $document->name ?? 'Untitled';

        return self::log(
            'download',
            'Document Downloaded',
            'document',
            $document,
            array_merge(['downloaded_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} downloaded document: {$documentName}"
        );
    }

    public static function logBookAddedToReadingList(User $user, Model $book, array $metadata = []): UserActivity
    {
        $bookTitle = $book->title ?? 'Untitled';

        return self::log(
            'favorite',
            'Book Added to Reading List',
            'library',
            $book,
            array_merge(['added_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} added book to reading list: {$bookTitle}"
        );
    }

    public static function logBookRemovedFromReadingList(User $user, Model $book, array $metadata = []): UserActivity
    {
        $bookTitle = $book->title ?? 'Untitled';

        return self::log(
            'unfavorite',
            'Book Removed from Reading List',
            'library',
            $book,
            array_merge(['removed_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} removed book from reading list: {$bookTitle}"
        );
    }

    public static function logBookSubscription(User $user, Model $book, array $metadata = []): UserActivity
    {
        $bookTitle = $book->title ?? 'Untitled';

        return self::log(
            'subscribe',
            'Book Subscription',
            'library',
            $book,
            array_merge(['subscribed_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} subscribed to book: {$bookTitle}"
        );
    }

    public static function logMessengerTokenPurchase(User $user, array $metadata = []): UserActivity
    {
        return self::log(
            'purchase',
            'Messenger Tokens Purchased',
            'payment',
            null,
            array_merge(['purchased_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} purchased messenger tokens"
        );
    }

    public static function logAssignmentSubmission(User $user, Model $assignment, array $metadata = []): UserActivity
    {
        $assignmentTitle = $assignment->title ?? 'Untitled';

        return self::log(
            'submit',
            'Assignment Submitted',
            'assignment',
            $assignment,
            array_merge(['submitted_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} submitted assignment: {$assignmentTitle}"
        );
    }

    public static function logPageView(User $user, string $pageName, array $metadata = []): UserActivity
    {
        return self::log(
            'view',
            "Viewed {$pageName}",
            'content',
            null,
            array_merge(['viewed_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} viewed {$pageName}"
        );
    }

    public static function logResourceCreate(User $user, Model $resource, array $metadata = []): UserActivity
    {
        $resourceName = class_basename($resource);

        return self::log(
            'create',
            "{$resourceName} Created",
            'content',
            $resource,
            array_merge(['created_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} created {$resourceName}"
        );
    }

    public static function logResourceUpdate(User $user, Model $resource, array $metadata = []): UserActivity
    {
        $resourceName = class_basename($resource);

        return self::log(
            'update',
            "{$resourceName} Updated",
            'content',
            $resource,
            array_merge(['updated_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} updated {$resourceName}"
        );
    }

    public static function logResourceDelete(User $user, Model $resource, array $metadata = []): UserActivity
    {
        $resourceName = class_basename($resource);

        return self::log(
            'delete',
            "{$resourceName} Deleted",
            'content',
            $resource,
            array_merge(['deleted_at' => now()->toIso8601String()], $metadata),
            description: "{$user->name} deleted {$resourceName}"
        );
    }

    /**
     * Generate a description for an activity
     */
    protected static function generateDescription(string $type, string $name): string
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Unknown User';

        return "{$userName} {$name}";
    }

    /**
     * Enrich metadata with system information
     */
    protected static function enrichMetadata(array $metadata): array
    {
        return array_merge($metadata, [
            'request_method' => Request::method(),
            'request_path' => Request::path(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Retrieve user activities with filtering
     */
    public static function getUserActivities(
        int $userId,
        ?string $category = null,
        ?string $activityType = null,
        int $limit = 50,
        int $page = 1
    ) {
        $query = UserActivity::where('user_id', $userId);

        if ($category) {
            $query->where('category', $category);
        }

        if ($activityType) {
            $query->where('activity_type', $activityType);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Get activity statistics for a user
     */
    public static function getUserActivityStatistics(int $userId, int $days = 7)
    {
        $startDate = now()->subDays($days);

        return [
            'total_activities' => UserActivity::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'by_category' => UserActivity::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->groupBy('category')
                ->selectRaw('category, count(*) as count')
                ->get()
                ->pluck('count', 'category'),
            'by_activity_type' => UserActivity::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->groupBy('activity_type')
                ->selectRaw('activity_type, count(*) as count')
                ->get()
                ->pluck('count', 'activity_type'),
            'last_activity' => UserActivity::where('user_id', $userId)
                ->latest()
                ->first(),
        ];
    }
}
