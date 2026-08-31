<?php

namespace App\BookShop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registered under its own alias ('bookshop.guest'), NOT as an override of
 * the host app's 'guest' alias, for the same reason as Authenticate above.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route(
                    $guard === 'bookshop_customer' ? 'bookshop.shop.home' : 'bookshop.staff.dashboard'
                );
            }
        }

        return $next($request);
    }
}
