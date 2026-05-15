<?php
/**
 * Proctoring Service Provider
 *
 * Registers the ProctoringManager, binds drivers to the container,
 * and publishes configuration. Enables the pluggable architecture
 * by allowing custom drivers to be registered via the manager.
 */
namespace App\Providers;

use App\Contracts\ProctoringDriverInterface;
use App\Services\Proctoring\ProctoringManager;
use Illuminate\Support\ServiceProvider;

class ProctoringServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/proctoring.php', 'proctoring');

        $this->app->singleton(ProctoringManager::class, function ($app) {
            return new ProctoringManager($app);
        });

        $this->app->bind(ProctoringDriverInterface::class, function ($app) {
            return $app->make(ProctoringManager::class)->driver();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/proctoring.php' => config_path('proctoring.php'),
        ], 'proctoring-config');
    }
}
