<?php

namespace App\BookShop\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Registered under its own alias ('bookshop.auth'), NOT as an override of
 * the host app's 'auth' alias — overriding 'auth' globally would break
 * unauthenticated redirects for every other guard in the app.
 */
class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        return $request->is('bookshop/shop*')
            ? route('bookshop.shop.login')
            : route('bookshop.staff.login');
    }

    /**
     * Overrides the base implementation to deliberately skip
     * Auth::shouldUse($guard). The base Authenticate middleware makes
     * the matched guard the DEFAULT guard for the rest of the request —
     * harmless for an app with one custom guard, but a real bug here:
     * any bare auth()->user() call elsewhere in the same request (host
     * app views, shared layouts, error pages) would then resolve against
     * bookshop_staff/bookshop_customer instead of the host app's own
     * 'web' guard. That's exactly what broke components/app.blade.php's
     * auth()->user()->canAccessCrossSchool() check — it silently got
     * handed a BookShop Customer instead of an App\Models\User.
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return;
            }
        }

        $this->unauthenticated($request, $guards);
    }
}
