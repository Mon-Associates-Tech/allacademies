<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Contracts\ExamDashboardServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Models\AcademicSubject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamDashboardService implements ExamDashboardServiceInterface
{
    public function listForOwner(int $userId, array $filters = []): LengthAwarePaginator
    {
        $search         = (string) ($filters['search'] ?? '');
        $status         = (string) ($filters['status'] ?? '');
        $subjectId      = (string) ($filters['subject'] ?? '');
        $sortBy         = $filters['sort_by'] ?? 'created_at';
        $sortDirection  = strtolower($filters['sort_direction'] ?? 'desc');

        // Whitelist to prevent SQL injection
        $allowedSorts = ['title', 'created_at', 'status', 'access_code', 'submissions_count', 'subject'];
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }

        $query = GeneralExam::where('user_id', $userId)
            ->with('academicSubject') // Eager load to prevent N+1 queries in Blade
            ->withCount(['sections', 'questions', 'submissions']);

        // Safe sorting by relationship name using a subquery
        if ($sortBy === 'subject') {
            $query->orderBy(
                AcademicSubject::select('name')
                    ->whereColumn('academic_subjects.id', 'general_exams.academic_subject_id')
                    ->limit(1),
                $sortDirection
            );
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('access_code', 'like', "%{$search}%")
                  ->orWhereHas('academicSubject', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($subjectId !== '') {
            $query->where('academic_subject_id', $subjectId);
        }

        return $query->paginate(12)->withQueryString();
    }

    public function summaryForOwner(int $userId): array
    {
        $examIds = GeneralExam::where('user_id', $userId)->pluck('id');
        if ($examIds->isEmpty()) {
            return [
                'total_exams' => 0,
                'total_submissions' => 0,
                'avg_score' => 0,
                'auto_gradable' => 0,
                'manual_review' => 0,
                'submission_trend' => [],
                'exam_status_distribution' => [],
            ];
        }

        $totalSubmissions = GeneralExamSubmission::whereIn('general_exam_id', $examIds)->count();
        $avgScore = (float) (GeneralExamSubmission::whereIn('general_exam_id', $examIds)->avg('percentage') ?? 0);

        $types = GeneralExamQuestion::whereIn('general_exam_id', $examIds)
            ->selectRaw("SUM(CASE WHEN type IN ('multiple_choice','true_false') THEN 1 ELSE 0 END) as auto_gradable")
            ->selectRaw("SUM(CASE WHEN type IN ('short_answer','essay') THEN 1 ELSE 0 END) as manual_review")
            ->first();

        $submissionTrend = GeneralExamSubmission::whereIn('general_exam_id', $examIds)
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $examStatusDistribution = GeneralExam::where('user_id', $userId)
            ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft")
            ->selectRaw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published")
            ->selectRaw("SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived")
            ->first();

        return [
            'total_exams' => $examIds->count(),
            'total_submissions' => $totalSubmissions,
            'avg_score' => round($avgScore, 1),
            'auto_gradable' => (int) ($types->auto_gradable ?? 0),
            'manual_review' => (int) ($types->manual_review ?? 0),
            'submission_trend' => $submissionTrend,
            'exam_status_distribution' => [
                'Draft' => (int) ($examStatusDistribution->draft ?? 0),
                'Published' => (int) ($examStatusDistribution->published ?? 0),
                'Archived' => (int) ($examStatusDistribution->archived ?? 0),
            ],
        ];
    }

    public function sectionNavigator(GeneralExam $exam): array
    {
        $sections = $exam->sections()->withCount('questions')->get();

        return $sections->map(function ($section, $index) {
            return [
                'index' => $index + 1,
                'title' => $section->title,
                'instructions' => $section->instructions,
                'time_limit_minutes' => $section->time_limit_minutes,
                'question_count' => $section->questions_count,
            ];
        })->all();
    }
}