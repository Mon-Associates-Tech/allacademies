<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplyUserPreferences
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $defaultGuard = config('auth.defaults.guard');

            if (Auth::guard($defaultGuard)->check()) {
                $user = Auth::guard($defaultGuard)->user();

                if ($user) {
                    // Safely wrap preferences in a Collection.
                    // If it's null, an array, or already a Collection, this handles it gracefully.
                    $preferences = collect($user->preferences);

                    // Apply theme preference
                    $theme = $preferences->where('key', 'theme')->first();
                    if ($theme && isset($theme->value) && in_array($theme->value, ['light', 'dark'], true)) {
                        view()->share('user_theme', $theme->value);
                    }

                    // Apply font preference
                    $font = $preferences->where('key', 'font')->first();
                    if ($font && isset($font->value)) {
                        view()->share('user_font', $font->value);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('ApplyUserPreferences middleware error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $next($request);
    }
}
