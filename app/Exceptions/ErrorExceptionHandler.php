<?php

namespace App\Exceptions;

use App\Services\ErrorNotificationService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class ErrorExceptionHandler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \ErrorException::class, // Skip memory-related errors
    ];

    /**
     * Report or log an exception.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        // Log memory usage for debugging
        \Log::debug('Memory usage in Handler::report', ['memory' => memory_get_usage(true) / 1024 / 1024 .' MB']);

        // Prevent recursive notifications
        static $notificationSent = false;

        if ($notificationSent) {
            \Log::debug('Skipping notification due to previous send');

            return;
        }

        if ($this->shouldReport($exception) && app()->bound(ErrorNotificationService::class)) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;

            if ($statusCode >= 500) {
                // Rate limit to 1 notification per minute per exception type
                $key = 'error-notification:'.get_class($exception);
                if (RateLimiter::tooManyAttempts($key, 1)) {
                    \Log::warning('Rate limit exceeded for error notification', [
                        'exception' => get_class($exception),
                    ]);

                    return;
                }

                RateLimiter::hit($key, 60); // 1 minute lockout

                try {
                    $errorService = app(ErrorNotificationService::class);
                    $user = auth()->user();
                    $errorService->sendErrorNotification(
                        substr($exception->getMessage(), 0, 500), // Truncate message
                        [
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'url' => substr(request()->fullUrl(), 0, 255),
                            'method' => request()->method(),
                            'user_id' => $user ? $user->id : 'Guest',
                            'user_name' => $user ? $user->name : 'Guest',
                            'trace' => collect($exception->getTrace())->take(5)->map(fn($t) => [
                                'file' => isset($t['file']) ? basename($t['file']) : 'unknown',
                                'line' => $t['line'] ?? 0,
                                'class' => $t['class'] ?? '',
                                'function' => $t['function'] ?? '',
                            ])->toArray(),
                        ]
                    );
                    $notificationSent = true;
                    \Log::debug('Notification sent successfully');
                } catch (\Throwable $e) {
                    \Log::error('Failed to send error notification', [
                        'original_error' => substr($exception->getMessage(), 0, 100),
                        'notification_error' => substr($e->getMessage(), 0, 100),
                    ]);
                }
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
