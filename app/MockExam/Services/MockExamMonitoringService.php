<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubmission;
use Illuminate\Support\Collection;

class MockExamMonitoringService
{
    public function getActiveParticipants(MockExam $mockExam): Collection
    {
        return $mockExam->submissions()
            ->where('status', MockExamSubmission::STATUS_IN_PROGRESS)
            ->with(['mockExam.subjectExams.sections'])
            ->get()
            ->map(function (MockExamSubmission $submission) {
                $totalQuestions = $submission->mockExam->getTotalQuestions();
                $answeredCount = $submission->getAnsweredCount();
                $progressPercent = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100, 1) : 0;

                $timeElapsed = $submission->started_at
                    ? $submission->started_at->diffForHumans(null, true)
                    : 'Not started';

                return [
                    'id' => $submission->id,
                    'name' => $submission->participant_name,
                    'email' => $submission->participant_email,
                    'section_index' => $submission->current_section_index,
                    'progress_percent' => $progressPercent,
                    'answered_count' => $answeredCount,
                    'total_questions' => $totalQuestions,
                    'time_elapsed' => $timeElapsed,
                    'is_idle' => $submission->isIdle(),
                    'last_activity' => $submission->last_activity_at?->diffForHumans() ?? 'Never',
                ];
            });
    }

    public function getExamStats(MockExam $mockExam): array
    {
        $submissions = $mockExam->submissions;

        return [
            'total_joined' => $submissions->count(),
            'in_progress' => $submissions->where('status', MockExamSubmission::STATUS_IN_PROGRESS)->count(),
            'submitted' => $submissions->whereIn('status', [
                MockExamSubmission::STATUS_SUBMITTED,
                MockExamSubmission::STATUS_AUTO_GRADED,
                MockExamSubmission::STATUS_MANUALLY_REVIEWED,
                MockExamSubmission::STATUS_FINAL,
            ])->count(),
        ];
    }
}
