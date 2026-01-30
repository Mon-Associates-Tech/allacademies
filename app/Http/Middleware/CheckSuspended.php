<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user's account is suspended.
     * If suspended, log them out and redirect to a suspended account page.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isSuspended()) {
            $user = Auth::user();
            $suspensionReason = $user->suspension_reason;
            $suspendedAt = $user->suspended_at;
            $suspendedBy = $user->suspendedBy?->name ?? 'Administrator';

            // Log the user out
            Auth::logout();

            // Invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Store suspension info in session for display
            session()->flash('suspension_reason', $suspensionReason);
            session()->flash('suspended_at', $suspendedAt);
            session()->flash('suspended_by', $suspendedBy);

            // Redirect to suspended page
            return redirect()->route('account.suspended');
        }

        return $next($request);
    }
}
