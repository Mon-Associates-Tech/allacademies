<?php

namespace App\BookShop\Providers;

use App\BookShop\Models\Customer;
use App\BookShop\Models\Staff;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Registers everything the BookShop module needs to function without
 * requiring manual edits to the host app's config/auth.php, so the
 * module stays extractable: dropping this provider (and the app/BookShop
 * + relevant migrations/routes) into another Laravel app is enough.
 */
class BookShopServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Two independent guards + providers, merged at runtime rather
        // than requiring the host app's config/auth.php to be edited.
        Config::set('auth.guards.bookshop_staff', [
            'driver' => 'session',
            'provider' => 'bookshop_staff',
        ]);

        Config::set('auth.guards.bookshop_customer', [
            'driver' => 'session',
            'provider' => 'bookshop_customer',
        ]);

        Config::set('auth.providers.bookshop_staff', [
            'driver' => 'eloquent',
            'model' => Staff::class,
        ]);

        Config::set('auth.providers.bookshop_customer', [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ]);

        // Separate password-reset broker tables/expiry per guard, mirroring
        // the same "keep it standalone" approach as the guards above.
        Config::set('auth.passwords.bookshop_staff', [
            'provider' => 'bookshop_staff',
            'table' => 'bookshop_staff_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ]);

        Config::set('auth.passwords.bookshop_customer', [
            'provider' => 'bookshop_customer',
            'table' => 'bookshop_customer_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ]);
    }

    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('bookshop.auth', \App\BookShop\Http\Middleware\Authenticate::class);
        $this->app['router']->aliasMiddleware('bookshop.guest', \App\BookShop\Http\Middleware\RedirectIfAuthenticated::class);
        $this->app['router']->aliasMiddleware(
            'bookshop.staff.branch-check',
            \App\BookShop\Http\Middleware\EnsureStaffHasBranch::class
        );
        $this->app['router']->aliasMiddleware(
            'bookshop.staff.superadmin-only',
            \App\BookShop\Http\Middleware\EnsureStaffIsSuperAdmin::class
        );

        // Kept in their own subfolder (database/migrations/bookshop) rather than
        // the app's root migrations folder, both so this doesn't double-register
        // the host app's own migrations and so the module stays cleanly extractable.
        $this->loadMigrationsFrom(database_path('migrations/bookshop'));
        $this->loadRoutesFrom(base_path('routes/bookshop.php'));
        $this->loadViewsFrom(resource_path('views/bookshop'), 'bookshop');
    }
}
