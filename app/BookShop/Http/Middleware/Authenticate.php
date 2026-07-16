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
}
