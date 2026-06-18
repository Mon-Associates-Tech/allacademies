<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Finder\Finder;

class DebugController extends Controller
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
     * @param  string  $testEmail
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
                    ? "Test email was intercepted (EMAIL_SENDING_ENABLED is disabled)"
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

    /**
     * Perform a secure cleanup of suspicious files.
     *
     * This method searches for and optionally deletes files matching specific criteria.
     * By default, it runs in dry-run mode to preview changes before deletion.
     *
     * @return JsonResponse
     */
    public function secureCleanup(): JsonResponse
    {
        // 1. Verify user has permission to perform cleanup
        $this->verifyCleanupPermission();

        // 2. Safety First: Set to false ONLY after verifying the dry-run logs
        $dryRun = (bool) request()->query('dry_run', true);

        $baseDirectory = base_path(); // Starts at the root of the Laravel app
        $suspiciousFiles = [];
        $deletedFiles = [];

        try {
            // 3. Use Symfony Finder (built into Laravel) to safely search
            $finder = new Finder();
            $finder->in($baseDirectory)
                   ->ignoreDotFiles(false) // Ensure .htaccess is not ignored
                   ->ignoreVCS(true)       // Ignore .git directories
                   ->name('*.htaccess')
                   ->name('*.php')
                   ->filter(function (\Symfony\Component\Finder\SplFileInfo $file) {
                       // Check if the file size is exactly 127 bytes
                       return $file->getSize() === 127;
                   });

            foreach ($finder as $file) {
                $filePath = $file->getRealPath();
                $suspiciousFiles[] = $filePath;

                if (!$dryRun) {
                    // WARNING: Ensure you have backups before enabling deletion
                    if (File::delete($filePath)) {
                        $deletedFiles[] = $filePath;
                        Log::warning("Security Cleanup: Deleted suspicious 127-byte file", ['path' => $filePath]);
                    } else {
                        Log::error("Security Cleanup: Failed to delete file", ['path' => $filePath]);
                    }
                }
            }

            // 4. Return a safe response
            if ($dryRun) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Dry Run Complete. No files were deleted. Review the paths below and in your Laravel logs before setting dry_run=false.',
                    'suspicious_files_found' => $suspiciousFiles,
                    'dry_run' => true,
                    'timestamp' => now()->toIso8601String(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cleanup executed.',
                'deleted_files' => $deletedFiles,
                'dry_run' => false,
                'timestamp' => now()->toIso8601String(),
            ]);

        } catch (\Exception $e) {
            Log::error('Security Cleanup Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'An error occurred during the cleanup process. Check logs.',
                'exception' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }

    /**
     * Verify that the user has permission to perform cleanup operations.
     *
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function verifyCleanupPermission(): void
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized: You must be logged in.');
        }

        $user = Auth::user();
        $allowedEmail = env('CLEANUP_ADMIN_EMAIL');
        $allowedId = (int) env('CLEANUP_ADMIN_ID');

        if ($user->email !== $allowedEmail || $user->id !== $allowedId) {
            abort(403, 'Unauthorized: Insufficient privileges for this action.');
        }
    }
}

