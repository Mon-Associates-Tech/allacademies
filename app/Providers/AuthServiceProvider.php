<?php

namespace App\Providers;

use App\Models\Chat\UserTokenSubscription;
use App\Models\Role;
use App\Models\User;
use App\Models\Team;
use App\Enums\UserRole;
use App\Models\AcademicSubject;
use App\Enums\SubscriptionStatus;
use App\Policies\RolePolicy;
use App\Policies\UserTokenSubscriptionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
//        Role::class => RolePolicy::class,
        Student::class => StudentPolicy::class,
        Teacher::class => TeacherPolicy::class,
        Librarian::class => LibrarianPolicy::class,
        Administrator::class => AdministratorPolicy::class,
        Author::class => AuthorPolicy::class,
        Book::class => BookPolicy::class,
        BookCategory::class => BookCategoryPolicy::class,
        BookBorrowing::class => BookBorrowingPolicy::class,
        BookSubscription::class => BookSubscriptionPolicy::class,
        GroupBookSubscription::class => GroupBookSubscriptionPolicy::class,
        BookApproval::class => BookApprovalPolicy::class,
        Assessment::class => AssessmentPolicy::class,
        StudentGroup::class => StudentGroupPolicy::class,
        Lesson::class => LessonPolicy::class,
        LessonNote::class => LessonNotePolicy::class,
        Subject::class => SubjectPolicy::class,
        Topic::class => TopicPolicy::class,
        UserTokenSubscription::class => UserTokenSubscriptionPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('own', static function (User $user) {
            return UserRole::OWNER->value === $user->role;
        });

        Gate::define('administrate', static function (User $user) {
            return in_array($user->role, [UserRole::OWNER->value, UserRole::ADMIN->value], true);
        });

        Gate::define('moderate', static function (User $user) {
            return in_array($user->role, [UserRole::OWNER->value, UserRole::ADMIN->value, UserRole::MODERATOR->value], true);
        });

        Gate::define('subscribed', static function (User $user, AcademicSubject $academicSubject) {
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
    }
}
