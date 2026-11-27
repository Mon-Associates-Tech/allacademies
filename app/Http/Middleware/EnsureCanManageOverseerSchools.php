<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCanManageOverseerSchools
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        abort_unless($user && ($user->isOwner() || $user->isSuperAdmin() || $user->hasRole('admin')), 403);

        return $next($request);
    }
}
