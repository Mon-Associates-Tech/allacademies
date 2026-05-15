<?php
/**
 * Proctoring Driver Interface
 *
 * Defines the contract for pluggable proctoring implementations.
 * Accepts any Eloquent model instance to enable polymorphic session
 * tracking across quizzes, exams, assignments, mocks, etc.
 */
namespace App\Contracts;


use App\Models\Proctoring\ExamProctoringSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

interface ProctoringDriverInterface
{
    /**
     * Initialize a new proctoring session for any examinable model.
     */
    public function initializeSession(Authenticatable $user, Model $proctorable): ExamProctoringSession;

    /**
     * Process a reported violation from the frontend.
     * Returns: ['action' => 'continue|warn|suspend|auto_submit', 'message' => string, 'violation_count' => int]
     */
    public function processViolation(ExamProctoringSession $session, string $type, array $metadata = []): array;

    /**
     * Clean up / end session gracefully.
     */
    public function terminateSession(ExamProctoringSession $session): void;
}
