<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

class EmailTestController extends Controller
{
    /**
     * Check the status of email sending functionality.
     *
     * @return array<string, mixed>
     */
    public function checkEmailSendingStatus(): array
    {
        $isEnabled = config('mail.enabled', true);
        $mailDriver = config('mail.driver');
        $mailFrom = config('mail.from');
        $environment = app()->environment();

        return [
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'email_sending_enabled' => (bool) $isEnabled,
            'mail_driver' => $mailDriver,
            'mail_from' => $mailFrom,
            'environment' => $environment,
            'message' => $isEnabled
                ? 'Email sending is currently ENABLED'
                : 'Email sending is currently DISABLED',
        ];
    }

    /**
     * Test sending an email and check if it was intercepted.
     *
     * @return array<string, mixed>
     */
    public function testEmailSending(string $testEmail = 'test@example.com'): array
    {
        $isEnabled = config('mail.enabled', true);
        $intercepted = false;

        // Listen for the MessageSending event
        Event::listen(MessageSending::class, function (MessageSending $event) use (&$intercepted): bool {
            $emailSendingEnabled = config('mail.enabled', true);

            if (! $emailSendingEnabled) {
                $intercepted = true;
            }

            return $emailSendingEnabled;
        });

        try {
            // Attempt to send a test email
            Mail::raw('This is a test email to verify email sending functionality.', function ($message) use ($testEmail): void {
                $message->to($testEmail)
                    ->subject('Test Email - Email Sending Status Check');
            });

            return [
                'status' => 'success',
                'timestamp' => now()->toIso8601String(),
                'test_email' => $testEmail,
                'email_sending_enabled' => $isEnabled,
                'email_intercepted' => $intercepted,
                'message' => $intercepted
                    ? 'Test email was intercepted (EMAIL_SENDING_ENABLED is disabled)'
                    : 'Test email was sent successfully (or queued for sending)',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'timestamp' => now()->toIso8601String(),
                'test_email' => $testEmail,
                'email_sending_enabled' => $isEnabled,
                'error_message' => $e->getMessage(),
                'exception' => class_basename($e),
            ];
        }
    }

    /**
     * Get comprehensive email sending status information.
     *
     * @return array<string, mixed>
     */
    public function getComprehensiveStatus(): array
    {
        $isEnabled = config('mail.enabled', true);
        $status = $this->checkEmailSendingStatus();

        return array_merge($status, [
            'listener_check' => [
                'listener_registered' => class_exists('App\Listeners\PreventDisabledMailSending'),
                'listener_class' => 'App\Listeners\PreventDisabledMailSending',
                'listener_event' => 'Illuminate\Mail\Events\MessageSending',
            ],
            'configuration_check' => [
                'config_file' => config_path('mail.php'),
                'env_variable' => 'EMAIL_SENDING_ENABLED',
                'current_value' => $isEnabled ? 'true' : 'false',
            ],
            'helpful_commands' => [
                'enable_emails' => 'export EMAIL_SENDING_ENABLED=true',
                'disable_emails' => 'export EMAIL_SENDING_ENABLED=false',
                'check_config' => 'php artisan config:show mail',
                'view_logs' => 'tail -f storage/logs/laravel.log',
            ],
        ]);
    }
}
