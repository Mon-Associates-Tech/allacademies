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
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user has active token subscription
            $hasActiveSubscription = $user->hasOpenAiTokens();
            $activeSubscription = $user->activeTokenSubscription;
            
            // Store in request for backend access
            $request->attributes->set('has_token_subscription', $hasActiveSubscription);
            $request->attributes->set('user_token_subscription', $activeSubscription);
            
            // Share with views (for regular Blade templates)
            view()->share('has_token_subscription', $hasActiveSubscription);
            view()->share('user_token_subscription', $activeSubscription);
            
            // Store in session for Livewire components to access
           // session(['has_token_subscription' => $hasActiveSubscription]);
            //session(['user_token_subscription' => $activeSubscription]);
        }

        return $next($request);
    }
}
