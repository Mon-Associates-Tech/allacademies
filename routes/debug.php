<?php

use App\Http\Controllers\Debug\EmailTestController;
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

Route::middleware(['auth', 'role:admin'])->prefix('debug')->name('debug.')->group(function () {

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
        Route::get('/status', [EmailTestController::class, 'checkEmailSendingStatus'])
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
        Route::get('/test', function (EmailTestController $controller) {
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
        Route::get('/comprehensive', [EmailTestController::class, 'getComprehensiveStatus'])
            ->name('comprehensive');
    });

});
