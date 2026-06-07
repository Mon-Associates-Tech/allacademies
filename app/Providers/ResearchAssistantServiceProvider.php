<?php

namespace App\Providers;

use App\Services\ResearchAssistantService;
use App\Services\ModelSelectionService;
use App\Services\TokenUsageService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ResearchAssistantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ResearchAssistantService::class, function ($app) {
            return new ResearchAssistantService(
                $app->make(\App\Services\ChatGPTService::class),
                $app->make(\App\Services\ModelSelectionService::class),
                $app->make(TokenUsageService::class)
            );
        });

        $this->app->singleton(ModelSelectionService::class, function ($app) {
            return new ModelSelectionService;
        });

        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/openai.php',
            'research_assistant'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/openai.php' => config_path('openai.php'),
        ], 'research-assistant-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../../resources/views/chats' => resource_path('views/research-assistant'),
        ], 'research-assistant-views');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views/research-assistant', 'research-assistant');

        // Share common data with views
        View::composer('livewire.research-assistant', function ($view) {
            $view->with([
                'chatConfig' => config('research_assistant'),
            ]);
        });
    }
}
