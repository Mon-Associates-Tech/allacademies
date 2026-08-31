<?php

namespace App\BookShop\Http\Middleware;

use App\BookShop\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Staff|null $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        abort_unless($staff && $staff->isSuperAdmin(), 403, 'Super admin access required.');

        return $next($request);
    }
}
