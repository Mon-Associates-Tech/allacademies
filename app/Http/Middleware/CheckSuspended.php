<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $defaultGuard = config('auth.defaults.guard');

            if (Auth::guard($defaultGuard)->check()) {
                $user = Auth::guard($defaultGuard)->user();

                // SAFE CHECK: Only proceed if the model actually has the isSuspended method
                if ($user && method_exists($user, 'isSuspended') && $user->isSuspended()) {

                    $suspensionReason = $user->suspension_reason ?? 'No reason provided';
                    $suspendedAt = $user->suspended_at;
                    $suspendedBy = optional($user->suspendedBy)->name ?? 'Administrator';

                    Auth::guard($defaultGuard)->logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    session()->flash('suspension_reason', $suspensionReason);
                    session()->flash('suspended_at', $suspendedAt);
                    session()->flash('suspended_by', $suspendedBy);

                    return redirect()->route('account.suspended');
                }
            }
        } catch (\Throwable $e) {
            \Log::error('CheckSuspended middleware error', [
                'error' => $e->getMessage(),
            ]);
        }

        return $next($request);
    }
}
