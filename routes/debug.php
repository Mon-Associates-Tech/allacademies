<?php

use App\Http\Controllers\Debug\DebugController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
|
| These routes are for debugging and testing purposes only.
| They should only be accessible in development/testing environments.
| In production, consider protecting these routes or disabling them entirely.
|
*/

Route::middleware(['auth', 'role:admin,owner'])->prefix('debug')->name('debug.')->group(function () {

    // Email Sending Status Routes
    Route::prefix('email')->name('email.')->group(function () {
        /**
         * GET /debug/email/status
         * Returns basic email sending configuration status.
         *
         * Example response:
         * {
         *   "status": "success",
         *   "email_sending_enabled": true,
         *   "mail_driver": "mailtrap",
         *   "message": "Email sending is currently ENABLED"
         * }
         */
        Route::get('/status', [DebugController::class, 'checkEmailSendingStatus'])
            ->name('status');

        /**
         * GET /debug/email/test
         * Attempts to send a test email and checks if it was intercepted.
         *
         * Query Parameters:
         * - email (optional): The test email address (default: test@example.com)
         *
         * Example: /debug/email/test?email=user@example.com
         *
         * Example response:
         * {
         *   "status": "success",
         *   "test_email": "user@example.com",
         *   "email_sending_enabled": true,
         *   "email_intercepted": false,
         *   "message": "Test email was sent successfully (or queued for sending)"
         * }
         */
        Route::get('/test', function (DebugController $controller) {
            $email = request()->query('email', 'test@example.com');

            return response()->json($controller->testEmailSending($email));
        })->name('test');

        /**
         * GET /debug/email/comprehensive
         * Returns comprehensive email sending status information including
         * listener registration, configuration, and helpful commands.
         *
         * Example response:
         * {
         *   "status": "success",
         *   "email_sending_enabled": true,
         *   "listener_check": {
         *     "listener_registered": true,
         *     "listener_class": "App\Listeners\PreventDisabledMailSending"
         *   },
         *   "configuration_check": {
         *     "env_variable": "EMAIL_SENDING_ENABLED",
         *     "current_value": "true"
         *   },
         *   "helpful_commands": {...}
         * }
         */
        Route::get('/comprehensive', [DebugController::class, 'getComprehensiveStatus'])
            ->name('comprehensive');
    });

    // System Cleanup Routes
    Route::prefix('cleanup')->name('cleanup.')->group(function () {
        /**
         * GET /debug/cleanup/secure
         * Performs a secure cleanup of suspicious files (127-byte .php and .htaccess files).
         *
         * Query Parameters:
         * - dry_run (optional): Boolean flag to preview changes without deleting (default: true)
         *
         * Example: /debug/cleanup/secure?dry_run=false
         *
         * Example response (dry-run mode):
         * {
         *   "status": "success",
         *   "message": "Dry Run Complete. No files were deleted...",
         *   "suspicious_files_found": ["/path/to/file.php", ...],
         *   "dry_run": true,
         *   "timestamp": "2026-06-18T12:00:00+00:00"
         * }
         */
        Route::get('/secure', [DebugController::class, 'secureCleanup'])
            ->name('secure');
    });

});
