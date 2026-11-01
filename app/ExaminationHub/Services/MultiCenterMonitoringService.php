<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use Illuminate\Support\Collection;

/**
 * Multi-center live monitoring service.
 *
 * Aggregates monitoring data from all published exams across all centers.
 * Unlike LiveMonitoringService which monitors local heartbeats, this service
 * uses synced submissions and proctoring logs to provide a central view
 * of all candidate activity across the network.
 */
class MultiCenterMonitoringService
{
    /**
     * Get monitoring data for all published exams across all centers.
     *
     * @param array|null $filters Optional filters: center_id, exam_id, status
     * @return array Aggregated monitoring data with per-center breakdown
     */
    public function getAllCentersMonitoringData(?array $filters = null): array
    {
        $centers = Center::orderBy('name')->get();
        $centerData = [];
        $allSubmissions = collect();
        $allProctoringLogs = collect();

        foreach ($centers as $center) {
            $centerSubmissions = $this->getCenterSubmissions($center, $filters);
            $centerProctoringLogs = $this->getCenterProctoringLogs($center, $filters);

            $centerData[$center->id] = [
                'center' => [
                    'id' => $center->id,
                    'uuid' => $center->uuid,
                    'code' => $center->code,
                    'name' => $center->name,
                    'is_active' => $center->is_active,
                    'last_seen_at' => $center->last_seen_at?->toIso8601String(),
                    'last_pull_at' => $center->last_pull_at?->toIso8601String(),
                    'last_push_at' => $center->last_push_at?->toIso8601String(),
                    'app_version' => $center->app_version,
                ],
                'stats' => $this->calculateCenterStats($centerSubmissions),
                'submissions' => $this->formatSubmissions($centerSubmissions),
                'proctoring_summary' => $this->summarizeProctoringLogs($centerProctoringLogs),
            ];

            $allSubmissions = $allSubmissions->merge($centerSubmissions);
            $allProctoringLogs = $allProctoringLogs->merge($centerProctoringLogs);
        }

        // Also include local (central) exam submissions
        $localData = $this->getLocalMonitoringData($filters);

        return [
            'centers' => $centerData,
            'local' => $localData,
            'overall_stats' => $this->calculateOverallStats($allSubmissions, $allProctoringLogs, $localData),
            'centers_count' => $centers->count(),
            'active_centers' => $centers->where('is_active', true)->count(),
        ];
    }

    /**
     * Get monitoring data for a specific exam across all centers.
     *
     * @return array Exam monitoring data with per-center breakdown
     */
    public function getExamMonitoringData(GeneralExam $exam): array
    {
        $centers = Center::orderBy('name')->get();
        $centerData = [];
        $allSubmissions = collect();

        foreach ($centers as $center) {
            $submissions = GeneralExamSubmission::where('center_id', $center->id)
                ->where('general_exam_id', $exam->id)
                ->with(['generalExamParticipant', 'answers'])
                ->orderBy('started_at', 'desc')
                ->get();

            if ($submissions->isEmpty()) {
                continue;
            }

            $proctoringLogs = ExamProctoringLog::where('center_id', $center->id)
                ->whereIn('general_exam_submission_id', $submissions->pluck('id'))
                ->orderByDesc('occurred_at')
                ->get();

            $centerData[$center->id] = [
                'center' => [
                    'id' => $center->id,
                    'code' => $center->code,
                    'name' => $center->name,
                    'is_active' => $center->is_active,
                    'last_push_at' => $center->last_push_at?->toIso8601String(),
                ],
                'stats' => $this->calculateCenterStats($submissions),
                'submissions' => $this->formatSubmissions($submissions, $exam),
                'proctoring_summary' => $this->summarizeProctoringLogs($proctoringLogs),
            ];

            $allSubmissions = $allSubmissions->merge($submissions);
        }

        // Include local submissions
        $localSubmissions = GeneralExamSubmission::where('general_exam_id', $exam->id)
            ->whereNull('center_id')
            ->with(['generalExamParticipant', 'answers'])
            ->orderBy('started_at', 'desc')
            ->get();

        $localProctoringLogs = ExamProctoringLog::whereNull('center_id')
            ->whereIn('general_exam_submission_id', $localSubmissions->pluck('id'))
            ->orderByDesc('occurred_at')
            ->get();

        return [
            'exam' => [
                'id' => $exam->id,
                'uuid' => $exam->uuid,
                'title' => $exam->title,
                'duration_minutes' => $exam->duration_in_minutes,
                'total_questions' => $exam->questions()->count(),
                'starts_at' => $exam->starts_at?->toIso8601String(),
                'ends_at' => $exam->ends_at?->toIso8601String(),
                'status' => $exam->status,
                'access_code' => $exam->access_code,
            ],
            'centers' => $centerData,
            'local' => [
                'stats' => $this->calculateCenterStats($localSubmissions),
                'submissions' => $this->formatSubmissions($localSubmissions, $exam),
                'proctoring_summary' => $this->summarizeProctoringLogs($localProctoringLogs),
            ],
            'overall_stats' => $this->calculateOverallStats($allSubmissions, collect(), [
                'submissions' => $this->formatSubmissions($localSubmissions, $exam),
            ]),
        ];
    }

    /**
     * Get a list of all published exams with their center coverage.
     *
     * @return Collection<GeneralExam> with center_count and total_submissions
     */
    public function getPublishedExamsWithCoverage(): Collection
    {
        $exams = GeneralExam::where('status', 'published')
            ->orderBy('starts_at', 'desc')
            ->get();

        return $exams->map(function ($exam) {
            $centerIds = GeneralExamSubmission::where('general_exam_id', $exam->id)
                ->whereNotNull('center_id')
                ->distinct()
                ->pluck('center_id');

            $totalSubmissions = GeneralExamSubmission::where('general_exam_id', $exam->id)->count();
            $submittedCount = GeneralExamSubmission::where('general_exam_id', $exam->id)
                ->whereNotNull('submitted_at')
                ->count();

            $exam->center_count = $centerIds->count();
            $exam->total_submissions = $totalSubmissions;
            $exam->submitted_count = $submittedCount;
            $exam->local_submissions = GeneralExamSubmission::where('general_exam_id', $exam->id)
                ->whereNull('center_id')
                ->count();

            return $exam;
        });
    }

    /**
     * Get submissions for a specific center with optional filters.
     */
    protected function getCenterSubmissions(Center $center, ?array $filters = null): Collection
    {
        $query = GeneralExamSubmission::where('center_id', $center->id)
            ->with(['exam', 'generalExamParticipant', 'answers'])
            ->orderBy('started_at', 'desc');

        if (isset($filters['exam_id'])) {
            $query->where('general_exam_id', $filters['exam_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    /**
     * Get proctoring logs for a specific center with optional filters.
     */
    protected function getCenterProctoringLogs(Center $center, ?array $filters = null): Collection
    {
        $query = ExamProctoringLog::where('center_id', $center->id)
            ->with(['submission.exam'])
            ->orderByDesc('occurred_at');

        if (isset($filters['exam_id'])) {
            $query->whereHas('submission', function ($q) use ($filters) {
                $q->where('general_exam_id', $filters['exam_id']);
            });
        }

        return $query->limit(500)->get();
    }

    /**
     * Get local (central) monitoring data for exams running directly on central.
     */
    protected function getLocalMonitoringData(?array $filters = null): array
    {
        $query = GeneralExamSubmission::whereNull('center_id')
            ->with(['exam', 'generalExamParticipant', 'answers'])
            ->orderBy('started_at', 'desc');

        if (isset($filters['exam_id'])) {
            $query->where('general_exam_id', $filters['exam_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $submissions = $query->get();
        $proctoringLogs = ExamProctoringLog::whereNull('center_id')
            ->whereIn('general_exam_submission_id', $submissions->pluck('id'))
            ->orderByDesc('occurred_at')
            ->limit(500)
            ->get();

        return [
            'stats' => $this->calculateCenterStats($submissions),
            'submissions' => $this->formatSubmissions($submissions),
            'proctoring_summary' => $this->summarizeProctoringLogs($proctoringLogs),
        ];
    }

    /**
     * Calculate statistics for a collection of submissions.
     */
    protected function calculateCenterStats(Collection $submissions): array
    {
        if ($submissions->isEmpty()) {
            return [
                'total_candidates' => 0,
                'joined' => 0,
                'in_progress' => 0,
                'submitted' => 0,
                'graded' => 0,
                'average_score' => 0,
                'average_percentage' => 0,
                'flagged_count' => 0,
            ];
        }

        $submitted = $submissions->filter(fn ($s) => $s->submitted_at !== null);
        $graded = $submissions->filter(fn ($s) => $s->score !== null);
        $inProgress = $submissions->filter(fn ($s) => $s->started_at !== null && $s->submitted_at === null);

        return [
            'total_candidates' => $submissions->count(),
            'joined' => $submissions->whereNotNull('started_at')->count(),
            'in_progress' => $inProgress->count(),
            'submitted' => $submitted->count(),
            'graded' => $graded->count(),
            'average_score' => $graded->count() > 0 ? round($graded->avg('score') ?? 0, 2) : 0,
            'average_percentage' => $graded->count() > 0 ? round($graded->avg('percentage') ?? 0, 2) : 0,
            'flagged_count' => $submissions->where('is_flagged', true)->count(),
        ];
    }

    /**
     * Format submissions for the view.
     */
    protected function formatSubmissions(Collection $submissions, ?GeneralExam $exam = null): array
    {
        return $submissions->map(function ($item) use ($exam) {
            $examData = $exam ?? $item->exam;
            $durationMinutes = $examData?->duration_in_minutes ?? 60;

            $answers = $item->answers ?? collect();
            $answeredCount = $answers->count();
            $totalQuestions = $examData?->questions()->count() ?? 0;

            $participant = $item->generalExamParticipant;
            return [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'participant_name' => $participant?->name ?? $item->participant_name ?? 'Unknown',
                'participant_email' => $participant?->email ?? $item->participant_email ?? '',
                'index_number' => $item->participant_index_number ?? $participant?->index_number ?? '',
                'status' => $item->status,
                'started_at' => $item->started_at?->toIso8601String(),
                'submitted_at' => $item->submitted_at?->toIso8601String(),
                'score' => $item->score,
                'percentage' => $item->percentage,
                'grade' => $item->grade,
                'answers_count' => $answeredCount,
                'total_questions' => $totalQuestions,
                'progress' => $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0,
                'is_flagged' => $item->is_flagged ?? false,
                'extra_time_minutes' => $item->extra_time_minutes ?? 0,
                'exam_title' => $examData?->title ?? '',
                'exam_uuid' => $examData?->uuid ?? '',
            ];
        })->values()->toArray();
    }

    /**
     * Summarize proctoring logs for a center.
     */
    protected function summarizeProctoringLogs(Collection $logs): array
    {
        if ($logs->isEmpty()) {
            return [
                'total_events' => 0,
                'high_severity' => 0,
                'medium_severity' => 0,
                'low_severity' => 0,
                'recent_events' => [],
            ];
        }

        return [
            'total_events' => $logs->count(),
            'high_severity' => $logs->where('severity', 'high')->count(),
            'medium_severity' => $logs->where('severity', 'medium')->count(),
            'low_severity' => $logs->where('severity', 'low')->count(),
            'recent_events' => $logs->take(20)->map(fn ($log) => [
                'id' => $log->id,
                'event_type' => $log->event_type,
                'severity' => $log->severity,
                'participant_name' => $log->submission?->participant?->name ?? 'Unknown',
                'occurred_at' => $log->occurred_at?->toIso8601String(),
            ])->values()->toArray(),
        ];
    }

    /**
     * Calculate overall statistics across all centers.
     */
    protected function calculateOverallStats(
        Collection $allSubmissions,
        Collection $allProctoringLogs,
        array $localData
    ): array {
        $localSubmissions = collect($localData['submissions'] ?? []);
        $combinedSubmissions = $allSubmissions->merge($localSubmissions);

        $submitted = $combinedSubmissions->filter(fn ($s) => isset($s['submitted_at']) && $s['submitted_at'] !== null);
        $graded = $combinedSubmissions->filter(fn ($s) => isset($s['score']) && $s['score'] !== null);

        return [
            'total_candidates' => $combinedSubmissions->count(),
            'joined' => $combinedSubmissions->filter(fn ($s) => isset($s['started_at']) && $s['started_at'] !== null)->count(),
            'in_progress' => $combinedSubmissions->filter(fn ($s) => isset($s['started_at'], $s['submitted_at']) && $s['started_at'] !== null && $s['submitted_at'] === null)->count(),
            'submitted' => $submitted->count(),
            'graded' => $graded->count(),
            'average_score' => $graded->count() > 0 ? round($graded->avg('score') ?? 0, 2) : 0,
            'average_percentage' => $graded->count() > 0 ? round($graded->avg('percentage') ?? 0, 2) : 0,
            'total_proctoring_events' => $allProctoringLogs->count(),
            'high_severity_events' => $allProctoringLogs->where('severity', 'high')->count(),
        ];
    }
}
