<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrackUserLoginActivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $user->update([
                'is_online' => true,
                'last_seen_at' => now(),
            ]);

            // Store in cache for 5 minutes
            Cache::put('user-online-'.$user->id, true, now()->addMinutes(5));

            // Redirect to school onboarding if needed (only on web routes, not API)
            if ($request->expectsJson() === false && $user->needsSchoolOnboarding()) {
                // Exclude onboarding and verification routes to prevent loops
                if (! $request->is('onboarding/*', 'email/verify/*', 'email/resend', 'logout')) {
                    return redirect()->route('onboarding.school-setup');
                }
            }
        }

        return $next($request);
    }
}
