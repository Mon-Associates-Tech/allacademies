<?php

namespace App\MockExam\Providers;

use App\MockExam\Services\MockExamCreationService;
use App\MockExam\Services\MockExamGradingService;
use App\MockExam\Services\MockExamParticipantService;
use App\MockExam\Services\MockExamPdfService;
use App\MockExam\Services\MockExamQuestionService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MockExamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Services are constructor-injectable as concretes, but explicit
        // bindings make them easy to swap in tests.
        $this->app->singleton(MockExamGradingService::class);
        $this->app->singleton(MockExamParticipantService::class);
        $this->app->singleton(MockExamPdfService::class);

        $this->app->singleton(MockExamQuestionService::class);

        $this->app->singleton(MockExamCreationService::class, function ($app) {
            return new MockExamCreationService(
                $app->make(MockExamQuestionService::class)
            );
        });
    }

    public function boot(): void
    {
        $this->registerRouteBindings();
        $this->loadRoutes();
    }

    private function registerRouteBindings(): void
    {
        Route::bind('mockExam', function ($value) {
            return \App\MockExam\Models\MockExam::findOrFail($value);
        });
    }

    private function loadRoutes(): void
    {
        // Routes are grouped under the 'web' middleware inside the file itself,
        // so we just include the file here without extra wrapping.
        Route::group([], base_path('routes/mock-exams.php'));
    }
}

/*
|--------------------------------------------------------------------------
| Registration
|--------------------------------------------------------------------------
| Add the provider to bootstrap/providers.php (Laravel 11/12):
|
|   return [
|       ...
|       App\MockExam\Providers\MockExamServiceProvider::class,
|   ];
|
| Or to config/app.php 'providers' array for older setups.
*/
