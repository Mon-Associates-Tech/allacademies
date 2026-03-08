<?php

namespace App\Facades;

use App\Services\UserActivityService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\UserActivity log(string $activityType, string $activityName, string $category, \Illuminate\Database\Eloquent\Model|null $subject = null, array $metadata = [], ?string $description = null, ?int $referenceId = null, ?int $userId = null)
 * @method static array logMany(array $activities)
 * @method static \App\Models\UserActivity logLogin(\App\Models\User $user)
 * @method static \App\Models\UserActivity logLogout(\App\Models\User $user)
 * @method static \App\Models\UserActivity logQuizStart(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $quiz, array $metadata = [])
 * @method static \App\Models\UserActivity logQuizSubmit(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $quiz, array $metadata = [])
 * @method static \App\Models\UserActivity logDocumentUpload(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $document, array $metadata = [])
 * @method static \App\Models\UserActivity logDocumentDownload(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $document, array $metadata = [])
 * @method static \App\Models\UserActivity logBookAddedToReadingList(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $book, array $metadata = [])
 * @method static \App\Models\UserActivity logBookRemovedFromReadingList(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $book, array $metadata = [])
 * @method static \App\Models\UserActivity logBookSubscription(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $book, array $metadata = [])
 * @method static \App\Models\UserActivity logMessengerTokenPurchase(\App\Models\User $user, array $metadata = [])
 * @method static \App\Models\UserActivity logAssignmentSubmission(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $assignment, array $metadata = [])
 * @method static \App\Models\UserActivity logPageView(\App\Models\User $user, string $pageName, array $metadata = [])
 * @method static \App\Models\UserActivity logResourceCreate(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $resource, array $metadata = [])
 * @method static \App\Models\UserActivity logResourceUpdate(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $resource, array $metadata = [])
 * @method static \App\Models\UserActivity logResourceDelete(\App\Models\User $user, \Illuminate\Database\Eloquent\Model $resource, array $metadata = [])
 * @method static \Illuminate\Pagination\Paginator getUserActivities(int $userId, ?string $category = null, ?string $activityType = null, int $limit = 50, int $page = 1)
 * @method static array getUserActivityStatistics(int $userId, int $days = 7)
 *
 * @see \App\Services\UserActivityService
 */
class ActivityLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UserActivityService::class;
    }
}
