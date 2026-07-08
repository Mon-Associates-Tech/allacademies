<?php

namespace App\Providers;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\AcademicSubject;
use App\Models\Chat\UserTokenSubscription;
use App\Models\Lms\Course;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Policies\CoursePolicy;
use App\Policies\RolePolicy;
use App\Policies\UserTokenSubscriptionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //        Role::class => RolePolicy::class,
        \App\Models\Student::class => \App\Policies\StudentPolicy::class,
        \App\Models\Teacher::class => \App\Policies\TeacherPolicy::class,
        \App\Models\Librarian::class => \App\Policies\LibrarianPolicy::class,
        \App\Models\Administrator::class => \App\Policies\AdministratorPolicy::class,
        \App\Models\Author::class => \App\Policies\AuthorPolicy::class,
        \App\Models\Book::class => \App\Policies\BookPolicy::class,
        \App\Models\BookCategory::class => \App\Policies\BookCategoryPolicy::class,
        \App\Models\BookBorrowing::class => \App\Policies\BookBorrowingPolicy::class,
        \App\Models\BookSubscription::class => \App\Policies\BookSubscriptionPolicy::class,
        \App\Models\GroupBookSubscription::class => \App\Policies\GroupBookSubscriptionPolicy::class,
        \App\Models\BookApproval::class => \App\Policies\BookApprovalPolicy::class,
        \App\Models\Assessment::class => \App\Policies\AssessmentPolicy::class,
        \App\Models\StudentGroup::class => \App\Policies\StudentGroupPolicy::class,
        \App\Models\Lesson::class => \App\Policies\LessonPolicy::class,
        \App\Models\LessonNote::class => \App\Policies\LessonNotePolicy::class,
        \App\Models\AcademicSubject::class => \App\Policies\SubjectPolicy::class,
        \App\Models\AcademicTopic::class => \App\Policies\TopicPolicy::class,
        \App\Models\Chat\UserTokenSubscription::class => \App\Policies\UserTokenSubscriptionPolicy::class,
        \App\Models\Lms\Course::class => \App\Policies\CoursePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('own', static function (User $user) {
            return UserRole::OWNER->value === $user->role->value;
        });

        Gate::define('administrate', static function (User $user) {
            return in_array($user->role->value, [UserRole::OWNER->value, UserRole::ADMIN->value], true);
        });

        Gate::define('moderate', static function (User $user) {
            return in_array($user->role->value, [UserRole::OWNER->value, UserRole::ADMIN->value, UserRole::MODERATOR->value], true);
        });

        Gate::define('subscribed', static function (User $user, AcademicSubject $academicSubject) {
            // Owners bypass subscription checks
            if ($user->role === UserRole::OWNER) {
                return true;
            }

            return $academicSubject
                ->subscriptions()
                ->where('team_id', $user->current_team_id)
                ->where('expires_at', '>', now())
                ->where('status', SubscriptionStatus::PAID)
                ->exists();
        });

        Gate::define('privileged', static function (User $user, Team $team) {
            return $team->owner_id === $user->id ||
                $team->members()
                    ->where('user_id', $user->id)
                    ->wherePivot('role', 'admin')
                    ->exists();
        });

        Gate::define('access-artisan-commands', static function (User $user) {
            return $user->isSuperAdmin() || $user->isOwner();
        });

        Gate::define('access-question-availability', static function (User $user) {
            return $user->isSuperAdmin() || $user->isOwner() || $user->isAdmin();
        });
    }
}
