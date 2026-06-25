<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExamProctoringEvent;
use App\MockExam\Models\MockExamSubmission;

class MockExamProctoringService
{
    public function recordEvent(
        MockExamSubmission $submission,
        string $type,
        ?array $details = null
    ): MockExamProctoringEvent {
        return MockExamProctoringEvent::create([
            'mock_exam_submission_id' => $submission->id,
            'event_type' => $type,
            'occurred_at' => now(),
            'details' => $details,
        ]);
    }

    public function getViolationCount(MockExamSubmission $submission): int
    {
        return MockExamProctoringEvent::forSubmission($submission->id)
            ->whereIn('event_type', ['tab_switch', 'fullscreen_exit', 'focus_loss'])
            ->count();
    }

    public function shouldAutoSubmit(MockExamSubmission $submission): bool
    {
        $exam = $submission->mockExam;

        if (! $exam->auto_submit_on_violation || $exam->tab_switch_limit === 0) {
            return false;
        }

        return $this->getViolationCount($submission) >= $exam->tab_switch_limit;
    }

    public function getProctoringReport(MockExamSubmission $submission): array
    {
        $events = MockExamProctoringEvent::forSubmission($submission->id)
            ->orderBy('occurred_at')
            ->get();

        return [
            'total_violations' => $events->whereIn('event_type', ['tab_switch', 'fullscreen_exit', 'focus_loss'])->count(),
            'tab_switches' => $events->where('event_type', 'tab_switch')->count(),
            'fullscreen_exits' => $events->where('event_type', 'fullscreen_exit')->count(),
            'paste_attempts' => $events->where('event_type', 'paste_attempt')->count(),
            'focus_losses' => $events->where('event_type', 'focus_loss')->count(),
            'events' => $events->map(fn ($e) => [
                'type' => $e->event_type,
                'time' => $e->occurred_at->format('H:i:s'),
                'details' => $e->details,
            ]),
        ];
    }
}
