<?php

namespace App\BookShop\Http\Middleware;

use App\BookShop\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * A branch admin with no branch_id is authenticated but not yet usable —
 * routed to a holding page instead of the dashboard until a superadmin
 * assigns them a branch. Superadmins are never subject to this check.
 */
class EnsureStaffHasBranch
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Staff|null $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $needsBranch = $staff && ! $staff->isSuperAdmin() && ! $staff->branch_id;

        if ($needsBranch && ! $request->routeIs('bookshop.staff.branch-pending', 'bookshop.staff.logout')) {
            return redirect()->route('bookshop.staff.branch-pending');
        }

        return $next($request);
    }
}
