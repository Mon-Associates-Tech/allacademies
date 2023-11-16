<?php

namespace App\Providers;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\AcademicGroup;
use App\Models\AcademicSubject;
use App\Models\User;
use App\Models\Team;
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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('own', function (User $user) {
            return UserRole::OWNER === $user->role;
        });

        Gate::define('administrate', function (User $user) {
            return in_array($user->role, [UserRole::OWNER, UserRole::ADMIN], true);
        });

        Gate::define('moderate', function (User $user) {
            return in_array($user->role, [UserRole::OWNER, UserRole::ADMIN, UserRole::MODERATOR], true);
        });

        Gate::define('subscribed', function (User $user, AcademicSubject $academicSubject) {
            return $academicSubject
                ->subscriptions()
                ->where('team_id', $user->current_team_id)
                ->where('expires_at', '>', now())
                ->where('status', SubscriptionStatus::PAID)
                ->exists();
        });

        Gate::define('privileged', function (User $user, Team $team) {
            return $team
                ->where(function ($query) use ($user) {
                    $query->whereHas('members', function ($query) use ($user) {
                        $query->where('team_user.user_id', $user->id)
                            ->where('team_user.role', 'admin');
                    });
                })
                ->orWhere('teams.owner_id', $user->id)
                ->exists();
        });
    }
}
