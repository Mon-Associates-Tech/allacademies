<?php

namespace App\Http;

use Akaunting\Firewall\Middleware\Ip;
use App\Http\Middleware\AcademicChatRateLimit;
use App\Http\Middleware\ApplyUserPreferences;
use App\Http\Middleware\AssignmentSessionMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AutomaticSchoolScoping;
use App\Http\Middleware\BackupUsersMiddleware;
use App\Http\Middleware\CheckDefaultPassword;
use App\Http\Middleware\CheckSuspended;
use App\Http\Middleware\CheckTokenSubscription;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\EnsureCanManageOverseerSchools;
use App\Http\Middleware\EnsureOverseerIsReadOnly;
use App\Http\Middleware\EnsureUserCanAccessSchool;
use App\Http\Middleware\EnsureUserIsParent;
use App\Http\Middleware\IncreaseUploadLimits;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\ProctorExamMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SchoolContextMiddleware;
use App\Http\Middleware\TrackUserLoginActivity;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        //        IncreaseUploadLimits::class,
        TrustProxies::class,
        HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        TrackUserLoginActivity::class,
        // \App\Http\Middleware\ValidateLicense::class,
        Ip::class,

    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            ApplyUserPreferences::class,
            CheckSuspended::class,
            CheckDefaultPassword::class,
            EnsureOverseerIsReadOnly::class,
            //            \App\Http\Middleware\SchoolContextMiddleware::class,
            //            \App\Http\Middleware\AutomaticSchoolScoping::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
            AutomaticSchoolScoping::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'auth.session' => AuthenticateSession::class,
        'cache.headers' => SetCacheHeaders::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'password.confirm' => RequirePassword::class,
        'signed' => ValidateSignature::class,
        'throttle' => ThrottleRequests::class,
        'verified' => EnsureEmailIsVerified::class,
        'role' => RoleMiddleware::class,
        'parent' => EnsureUserIsParent::class,
        'school.context' => SchoolContextMiddleware::class,
        'school.access' => EnsureUserCanAccessSchool::class,
        'chat.rate.limit' => AcademicChatRateLimit::class,
        'school.scope' => AutomaticSchoolScoping::class,
        'manage.overseer-schools' => EnsureCanManageOverseerSchools::class,
        'assignment.session' => AssignmentSessionMiddleware::class,
        'token.subscription' => CheckTokenSubscription::class,
        'proctor.exam' => ProctorExamMiddleware::class,
        'backup.users' => BackupUsersMiddleware::class,
    ];
}
