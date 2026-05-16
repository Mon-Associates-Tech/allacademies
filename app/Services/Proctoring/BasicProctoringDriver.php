<?php
/**
 * Basic Proctoring Driver
 *
 * Implements browser-based proctoring logic with polymorphic support.
 * Initializes sessions for any examinable model and enforces violation thresholds.
 */
namespace App\Services\Proctoring;

use App\Contracts\ProctoringDriverInterface;
use App\Models\Proctoring\ExamProctoringSession;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BasicProctoringDriver implements ProctoringDriverInterface
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('proctoring');
    }

    public function initializeSession(Authenticatable $user, Model $proctorable): ExamProctoringSession
    {
        // Resolve school ID based on your multi-tenancy implementation
        $schoolId = $user->currentSchool?->id ?? $user->school_id ?? 1;

        return ExamProctoringSession::create([
            'user_id' => $user->getAuthIdentifier(),
            'proctorable_type' => get_class($proctorable),
            'proctorable_id' => $proctorable->getKey(),
            'school_id' => $schoolId,
            'session_token' => Str::random(32),
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function processViolation(ExamProctoringSession $session, string $type, array $metadata = []): array
    {
        $session->recordViolation($type, $metadata);
        $maxAllowed = $this->config['violations']['max_allowed'];
        $warningThreshold = $this->config['violations']['warning_threshold'];
        $count = $session->violation_count;

        $action = 'continue';
        $message = '';

        if ($count >= $maxAllowed && $this->config['violations']['auto_submit_on_exceed']) {
            $session->update(['status' => 'auto_submitted', 'ended_at' => now()]);
            $action = 'auto_submit';
            $message = 'Maximum violations exceeded. Exam auto-submitted.';
        } elseif ($count >= $warningThreshold) {
            $session->update(['status' => 'warning']);
            $action = 'warn';
            $message = "Warning: You have triggered {$count} violations. Further violations may result in auto-submission.";
        } else {
            $message = "Violation recorded: {$type}. Please maintain exam integrity.";
        }

        return ['action' => $action, 'message' => $message, 'violation_count' => $count];
    }

    public function terminateSession(ExamProctoringSession $session): void
    {
        if (in_array($session->status, ['active', 'warning'])) {
            $session->update(['status' => 'completed', 'ended_at' => now()]);
        }
    }
}
