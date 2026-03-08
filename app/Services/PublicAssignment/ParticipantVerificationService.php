<?php

namespace App\Services\PublicAssignment;

use App\Models\PublicAssignment;
use App\Models\PublicAssignmentParticipant;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ParticipantVerificationService
{
    /**
     * Send verification email to participant
     */
    public function sendVerificationEmail(
        PublicAssignmentParticipant $participant,
        PublicAssignment $assignment
    ): bool {
        // Generate new verification token if needed
        if (empty($participant->verification_token)) {
            $participant->update([
                'verification_token' => Str::random(64),
            ]);
        }

        // Check if we can send (rate limiting)
        if (! $participant->canResendVerification()) {
            return false;
        }

        $verificationUrl = $this->generateVerificationUrl($participant, $assignment);

        try {
            Mail::send(
                'emails.public-assignment.verify-participant',
                [
                    'participant' => $participant,
                    'assignment' => $assignment,
                    'verificationUrl' => $verificationUrl,
                ],
                function ($message) use ($participant, $assignment) {
                    $message->to($participant->email, $participant->name)
                        ->subject("Verify your email for: {$assignment->title}");
                }
            );

            $participant->update([
                'verification_sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send participant verification email', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate verification URL
     */
    public function generateVerificationUrl(
        PublicAssignmentParticipant $participant,
        PublicAssignment $assignment
    ): string {
        return URL::temporarySignedRoute(
            'public-assignments.verify-email',
            now()->addHours(24),
            [
                'token' => $participant->verification_token,
                'assignment' => $assignment->access_code,
            ]
        );
    }

    /**
     * Verify participant email
     */
    public function verifyEmail(string $token, string $accessCode): array
    {
        $participant = PublicAssignmentParticipant::findByVerificationToken($token);

        if (! $participant) {
            return [
                'success' => false,
                'error' => 'Invalid or expired verification token.',
            ];
        }

        if ($participant->isEmailVerified()) {
            return [
                'success' => true,
                'message' => 'Email already verified.',
                'participant' => $participant,
            ];
        }

        $assignment = PublicAssignment::findByAccessCode($accessCode);

        if (! $assignment) {
            return [
                'success' => false,
                'error' => 'Assignment not found.',
            ];
        }

        $participant->markEmailAsVerified();

        return [
            'success' => true,
            'message' => 'Email verified successfully.',
            'participant' => $participant->fresh(),
            'assignment' => $assignment,
        ];
    }

    /**
     * Resend verification email
     */
    public function resendVerification(
        PublicAssignmentParticipant $participant,
        PublicAssignment $assignment
    ): array {
        if ($participant->isEmailVerified()) {
            return [
                'success' => false,
                'error' => 'Email is already verified.',
            ];
        }

        if (! $participant->canResendVerification()) {
            $waitTime = $participant->verification_sent_at->addMinute()->diffForHumans();

            return [
                'success' => false,
                'error' => "Please wait {$waitTime} before requesting another verification email.",
            ];
        }

        // Generate new token
        $participant->update([
            'verification_token' => Str::random(64),
        ]);

        $sent = $this->sendVerificationEmail($participant, $assignment);

        if ($sent) {
            return [
                'success' => true,
                'message' => 'Verification email sent successfully.',
            ];
        }

        return [
            'success' => false,
            'error' => 'Failed to send verification email. Please try again later.',
        ];
    }

    /**
     * Send result notification email
     */
    public function sendResultNotification(
        PublicAssignmentParticipant $participant,
        PublicAssignment $assignment
    ): bool {
        $resultUrl = $this->generateResultUrl($participant);

        try {
            Mail::send(
                'emails.public-assignment.result-notification',
                [
                    'participant' => $participant,
                    'assignment' => $assignment,
                    'resultUrl' => $resultUrl,
                ],
                function ($message) use ($participant, $assignment) {
                    $message->to($participant->email, $participant->name)
                        ->subject("Results Available: {$assignment->title}");
                }
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send result notification email', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate result access URL
     */
    public function generateResultUrl(PublicAssignmentParticipant $participant): string
    {
        return route('public-assignments.results', [
            'token' => $participant->result_access_token,
        ]);
    }

    /**
     * Validate result access token
     */
    public function validateResultToken(string $token): ?PublicAssignmentParticipant
    {
        return PublicAssignmentParticipant::findByResultToken($token);
    }

    /**
     * Register participant and send verification
     */
    public function registerAndVerify(
        array $data,
        PublicAssignment $assignment
    ): array {
        // Check if participant already exists
        $existingParticipant = PublicAssignmentParticipant::findByEmail($data['email']);

        if ($existingParticipant) {
            // If already verified, return success
            if ($existingParticipant->isEmailVerified()) {
                return [
                    'success' => true,
                    'participant' => $existingParticipant,
                    'already_verified' => true,
                    'message' => 'Email already verified. You can proceed to take the assignment.',
                ];
            }

            // Resend verification
            $this->sendVerificationEmail($existingParticipant, $assignment);

            return [
                'success' => true,
                'participant' => $existingParticipant,
                'already_verified' => false,
                'message' => 'Verification email sent. Please check your inbox.',
            ];
        }

        // Create new participant
        $participant = PublicAssignmentParticipant::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
        ]);

        // Send verification email
        $this->sendVerificationEmail($participant, $assignment);

        return [
            'success' => true,
            'participant' => $participant,
            'already_verified' => false,
            'message' => 'Registration successful. Please check your email to verify your address.',
        ];
    }

    /**
     * Bulk send result notifications for an assignment
     */
    public function sendBulkResultNotifications(PublicAssignment $assignment): array
    {
        $submissions = $assignment->submissions()
            ->where('participant_type', PublicAssignmentParticipant::class)
            ->whereNotNull('submitted_at')
            ->with('participant')
            ->get();

        $results = [
            'total' => $submissions->count(),
            'sent' => 0,
            'failed' => 0,
        ];

        foreach ($submissions as $submission) {
            $participant = $submission->participant;

            if ($participant && $participant instanceof PublicAssignmentParticipant) {
                $sent = $this->sendResultNotification($participant, $assignment);

                if ($sent) {
                    $results['sent']++;
                } else {
                    $results['failed']++;
                }
            }
        }

        return $results;
    }

    /**
     * Check if participant can take assignment
     */
    public function canParticipantTakeAssignment(
        PublicAssignmentParticipant $participant,
        PublicAssignment $assignment
    ): array {
        if (! $participant->isEmailVerified()) {
            return [
                'can_take' => false,
                'reason' => 'email_not_verified',
                'message' => 'Please verify your email address before taking the assignment.',
            ];
        }

        if (! $assignment->isActive()) {
            return [
                'can_take' => false,
                'reason' => 'assignment_not_active',
                'message' => 'This assignment is not currently available.',
            ];
        }

        $attemptCount = $assignment->getParticipantAttemptCount(
            PublicAssignmentParticipant::class,
            $participant->id
        );

        if ($attemptCount >= $assignment->max_attempts) {
            return [
                'can_take' => false,
                'reason' => 'max_attempts_reached',
                'message' => 'You have reached the maximum number of attempts for this assignment.',
            ];
        }

        // Check if there's an in-progress submission
        $existingSubmission = $assignment->submissions()
            ->where('participant_type', PublicAssignmentParticipant::class)
            ->where('participant_id', $participant->id)
            ->whereIn('status', ['not_started', 'in_progress'])
            ->first();

        if ($existingSubmission) {
            return [
                'can_take' => true,
                'has_existing_submission' => true,
                'submission' => $existingSubmission,
                'message' => 'You have an in-progress submission.',
            ];
        }

        return [
            'can_take' => true,
            'has_existing_submission' => false,
            'message' => 'You can take this assignment.',
        ];
    }
}
