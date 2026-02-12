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
        try {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user && $user->isSuspended()) {
                    // Retrieve all data BEFORE logout/invalidation
                    $suspensionReason = $user->suspension_reason;
                    $suspendedAt = $user->suspended_at;
                    $suspendedBy = optional($user->suspendedBy)->name ?? 'Administrator';

                    // Log the user out
                    Auth::logout();

                    // Invalidate the session
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    // Store suspension info in new session for display
                    session()->flash('suspension_reason', $suspensionReason);
                    session()->flash('suspended_at', $suspendedAt);
                    session()->flash('suspended_by', $suspendedBy);

                    // Redirect to suspended page
                    return redirect()->route('account.suspended');
                }
            }
        } catch (\Throwable $e) {
            \Log::error('CheckSuspended middleware error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $next($request);
    }
}
