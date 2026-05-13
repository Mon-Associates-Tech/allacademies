<?php

namespace App\Providers;

use App\Examinations\Contracts\ExamCreationServiceInterface;
use App\Examinations\Contracts\ExamParticipantAccessServiceInterface;
use App\Examinations\Contracts\ExamSubmissionExportServiceInterface;
use App\Examinations\Services\ExamCreationService;
use App\Examinations\Services\ExamParticipantAccessService;
use App\Examinations\Services\ExamSubmissionExportService;
use App\Models\GradeScale;
use App\Policies\GradeScalePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class ExaminationsServiceProvider extends AuthServiceProvider
{
    /**
     * Policy mappings for the Examinations Hub.
     */
    protected $policies = [
        GradeScale::class => GradeScalePolicy::class,
    ];

    public function register(): void
    {
        // Core service bindings
        $this->app->bind(ExamCreationServiceInterface::class,          ExamCreationService::class);
        $this->app->bind(ExamParticipantAccessServiceInterface::class, ExamParticipantAccessService::class);
        $this->app->bind(ExamSubmissionExportServiceInterface::class,  ExamSubmissionExportService::class);
    }

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
