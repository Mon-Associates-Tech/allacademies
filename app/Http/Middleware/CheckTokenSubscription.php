<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            try {
                // Check if user has active subscription cycle with available tokens
                $user->load('subscriptionCycles');
                $hasActiveSubscription = $user->subscriptionCycles()->whereStatus('active')->exists();
                $activeCycle = $user->subscriptionCycles()->whereStatus('active')->first();

                // Share with views (for regular Blade templates)
                view()->share('has_token_subscription', $hasActiveSubscription);
                view()->share('user_token_subscription', $activeCycle);
            } catch (\Exception $e) {
                // Fallback to false if there's an error
                view()->share('has_token_subscription', false);
                view()->share('user_token_subscription', null);
            }
        } else {
            view()->share('has_token_subscription', false);
            view()->share('user_token_subscription', null);
        }

        return $next($request);
    }
}
