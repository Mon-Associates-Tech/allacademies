<?php

namespace App\Examinations\Services;

use App\Examinations\Contracts\ExamDashboardServiceInterface;
use App\Models\GeneralExam;
use App\Models\GeneralExamQuestion;
use App\Models\GeneralExamSubmission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamDashboardService implements ExamDashboardServiceInterface
{
    public function listForOwner(int $userId, array $filters = []): LengthAwarePaginator
    {
        $search = (string) ($filters['search'] ?? '');
        $status = (string) ($filters['status'] ?? '');

        $query = GeneralExam::where('user_id', $userId)
            ->withCount(['sections', 'questions', 'submissions'])
            ->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('access_code', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate(12);
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
            ];
        }

        $totalSubmissions = GeneralExamSubmission::whereIn('general_exam_id', $examIds)->count();
        $avgScore = (float) (GeneralExamSubmission::whereIn('general_exam_id', $examIds)->avg('percentage') ?? 0);

        $types = GeneralExamQuestion::whereIn('general_exam_id', $examIds)
            ->selectRaw("SUM(CASE WHEN type IN ('multiple_choice','true_false') THEN 1 ELSE 0 END) as auto_gradable")
            ->selectRaw("SUM(CASE WHEN type IN ('short_answer','essay') THEN 1 ELSE 0 END) as manual_review")
            ->first();

        return [
            'total_exams' => $examIds->count(),
            'total_submissions' => $totalSubmissions,
            'avg_score' => round($avgScore, 1),
            'auto_gradable' => (int) ($types->auto_gradable ?? 0),
            'manual_review' => (int) ($types->manual_review ?? 0),
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

