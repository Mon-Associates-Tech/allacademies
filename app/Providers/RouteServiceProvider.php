<?php

namespace App\Providers;

use App\Models\AcademicTopic;
use App\Models\EducationalResource;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        Route::model('academic_topic', AcademicTopic::class);
        Route::model('educationalResource', EducationalResource::class);

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Sync API routes for multi-center sync
            // Does not use the 'api' middleware group (which includes throttle:api at 60/min).
            // Instead, applies only the middleware we need plus throttle:sync (1000/min per center).
            Route::middleware([
                'throttle:sync',
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ])
                ->prefix('api')
                ->group(base_path('routes/sync.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Multi-center sync admin routes
            Route::middleware('web')
                ->group(base_path('routes/center-sync.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Sync API rate limiter — much higher limit for batch push/pull operations.
        // Centers push hundreds of submissions in batches, so 60/min is too low.
        RateLimiter::for('sync', function (Request $request) {
            // Identify by the center's bearer token, or fall back to IP.
            $key = $request->bearerToken() ?: $request->ip();
            return Limit::perMinute(1000)->by($key);
        });
    }
}
