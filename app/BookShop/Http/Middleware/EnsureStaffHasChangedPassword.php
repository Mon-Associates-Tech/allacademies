<?php

namespace App\BookShop\Http\Middleware;

use App\BookShop\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applied above the branch-check middleware (password change matters
 * regardless of whether a branch is assigned yet) but below logout - a
 * staff member always needs a way out even mid-forced-change.
 */
class EnsureStaffHasChangedPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Staff|null $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $mustChange = $staff && $staff->must_change_password;

        if ($mustChange && ! $request->routeIs('bookshop.staff.password.change', 'bookshop.staff.password.update', 'bookshop.staff.logout')) {
            return redirect()->route('bookshop.staff.password.change');
        }

        return $next($request);
    }
}
