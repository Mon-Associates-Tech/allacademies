<?php

namespace App\Http\Middleware;

use App\Models\StudentParent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsParent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check if user has any parent records
        $hasParentRole = StudentParent::where('user_id', $user->id)->exists();

        // Check if someone is impersonating (admin accessing as another user)
        $isImpersonating = session()->has('impersonated_by');

        // Allow access if:
        // 1. User is a parent, OR
        // 2. Someone is impersonating (admin is logged in as another user)
        if (! $hasParentRole && ! $isImpersonating) {
            abort(403, 'Access denied. You must be a parent to access this area.');
        }

        return $next($request);
    }
}
