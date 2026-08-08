<?php

namespace App\Providers;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\Administrator;
use App\Models\Assessment;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookApproval;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use App\Models\Chat\UserTokenSubscription;
use App\Models\GroupBookSubscription;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\Librarian;
use App\Models\Lms\Course;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\User;
use App\Policies\AdministratorPolicy;
use App\Policies\AssessmentPolicy;
use App\Policies\AuthorPolicy;
use App\Policies\BookApprovalPolicy;
use App\Policies\BookBorrowingPolicy;
use App\Policies\BookCategoryPolicy;
use App\Policies\BookPolicy;
use App\Policies\BookSubscriptionPolicy;
use App\Policies\CoursePolicy;
use App\Policies\GroupBookSubscriptionPolicy;
use App\Policies\LessonNotePolicy;
use App\Policies\LessonPolicy;
use App\Policies\LibrarianPolicy;
use App\Policies\RolePolicy;
use App\Policies\StudentGroupPolicy;
use App\Policies\StudentPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\TeacherPolicy;
use App\Policies\TopicPolicy;
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
        AcademicSubject::class => SubjectPolicy::class,
        AcademicTopic::class => TopicPolicy::class,
        UserTokenSubscription::class => UserTokenSubscriptionPolicy::class,
        Course::class => CoursePolicy::class,

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
