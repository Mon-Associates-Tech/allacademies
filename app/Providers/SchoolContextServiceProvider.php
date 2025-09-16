<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class SchoolContextServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Global query macro for cross-school queries (admin only)
        Builder::macro('crossSchool', function () {
            $user = auth()->user();

            if (!$user || (!$user->isSuperAdmin() && !$user->hasRole('owner'))) {
                abort(403, 'Unauthorized to access cross-school data');
            }

            return $this->withoutGlobalScopes();
        });

        // Gate for school access control
        Gate::define('access-school', function (User $user, $schoolId) {
            if ($user->isSuperAdmin() || $user->hasRole('owner')) {
                return true;
            }

            return $user->school_id == $schoolId;
        });

        // Gate for cross-school operations
        Gate::define('cross-school-access', function (User $user) {
            return $user->isSuperAdmin() || $user->hasRole('owner');
        });
    }
}
