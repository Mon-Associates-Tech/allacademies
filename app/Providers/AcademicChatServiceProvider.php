<?php

namespace App\Providers;

use App\Services\AcademicChatService;
use App\Services\ChatGPTService;
use App\Services\ModelSelectionService;
use App\Services\TokenUsageService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AcademicChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AcademicChatService::class, function ($app) {
            return new AcademicChatService(
                $app->make(\App\Services\ChatGPTService::class),
                $app->make(\App\Services\ModelSelectionService::class),
                $app->make(TokenUsageService::class)
            );
        });

        $this->app->singleton(ModelSelectionService::class, function ($app) {
            return new ModelSelectionService();
        });

        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/openai.php',
            'academic_chat'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/openai.php' => config_path('openai.php'),
        ], 'academic-chat-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../../resources/views/chats' => resource_path('views/chats'),
        ], 'academic-chat-views');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'chats');

        // Share common data with views
        View::composer('livewire.academic-chat', function ($view) {
            $view->with([
                'chatConfig' => config('academic_chat'),
            ]);
        });
    }
}
