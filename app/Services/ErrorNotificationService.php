<?php

namespace App\Services;

use App\Mail\ErrorNotification;
use Illuminate\Support\Facades\Mail;

class ErrorNotificationService
{
    /**
     * List of email recipients for error notifications
     *
     * @var array
     */
    protected $recipients;

    /**
     * Constructor to initialize recipients from config
     */
    public function __construct()
    {
        $this->recipients = config('error_handling.recipients', []);
    }

    /**
     * Send error notification email
     *
     * @param  string  $errorMessage  The error message to send
     * @param  array  $additionalData  Additional data to include in the email
     */
    public function sendErrorNotification(string $errorMessage, array $additionalData = []): bool
    {
        //        \Log::debug('Memory usage in ErrorNotificationService', ['memory' => memory_get_usage(true) / 1024 / 1024 . ' MB']);

        try {
            if (empty($this->recipients)) {
                //                \Log::warning('No recipients configured for error notifications');
                return false;
            }

            // Prepare minimal email data
            $emailData = [
                'error_message' => substr($errorMessage, 0, 500),
                'timestamp' => now()->toDateTimeString(),
                'additional_data' => array_map(function ($value) {
                    return is_string($value) ? substr($value, 0, 255) : $value;
                }, $additionalData),
                'environment' => config('app.env'),
                'app_name' => config('app.name'),
                'trace' => isset($additionalData['trace']) ? $additionalData['trace'] : null,
            ];

            // Send synchronously to avoid queue serialization issues
            foreach ($this->recipients as $recipient) {
                Mail::to($recipient)->send(new ErrorNotification($emailData));
            }

            //            \Log::info('Error notification sent successfully', [
            //                'recipients' => $this->recipients,
            //                'error_message' => substr($errorMessage, 0, 100),
            //            ]);

            return true;
        } catch (\Exception $e) {
            /*\Log::error('Failed to send error notification', [
                'error' => substr($e->getMessage(), 0, 100),
                'original_error' => substr($errorMessage, 0, 100),
            ]);*/
            return false;
        }
    }
}
