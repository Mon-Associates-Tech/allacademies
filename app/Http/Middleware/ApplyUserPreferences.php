<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
            // Apply theme preference BEFORE processing request
            if (auth()->check()) {
                $user = auth()->user();
                if ($user) {
                    $theme = $user->preferences->where('key', 'theme')->first();
                    if ($theme && in_array($theme->value, ['light', 'dark'])) {
                        view()->share('user_theme', $theme->value);
                    }

                    $font = $user->preferences->where('key', 'font')->first();
                    if ($font) {
                        view()->share('user_font', $font->value);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('ApplyUserPreferences middleware error', [
                'error' => $e->getMessage(),
            ]);
        }

        return $next($request);
    }
}
