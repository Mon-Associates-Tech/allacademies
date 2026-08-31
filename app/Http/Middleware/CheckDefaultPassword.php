<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDefaultPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->has_default_password) {
            if (!$request->routeIs('password.change') && !$request->routeIs('logout')) {
                // Regenerate CSRF token before redirect to ensure it's fresh for Livewire
                $request->session()->regenerateToken();
                
                return redirect()->route('password.change')
                    ->with('warning', 'You must change your default password before continuing.');
            }
        }

        return $next($request);
    }
}
