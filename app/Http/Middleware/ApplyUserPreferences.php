<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApplyUserPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Apply theme preference
        if (auth()->check()) {
            $theme = auth()->user()->preferences->where('key', 'theme')->first();
            if ($theme && in_array($theme->value, ['light', 'dark'])) {
                // We can use this in our views to apply the theme
                view()->share('user_theme', $theme->value);
            }

            $font = auth()->user()->preferences->where('key', 'font')->first();
            if ($font) {
                view()->share('user_font', $font->value);
            }
        }

        return $response;
    }
}
