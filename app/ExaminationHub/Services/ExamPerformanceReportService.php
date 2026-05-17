<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Services\ChatGPTService;
use Carbon\Carbon;

class ExamPerformanceReportService
{
    public function __construct(
        private ChatGPTService $chatGPTService
    ) {}

    public function generateReport(array $filters = [], bool $useAi = true): array
    {
        $startDate = $filters['start_date'] ?? now()->subMonth();
        $endDate = $filters['end_date'] ?? now();
        $user = auth()->user();
        $schoolId = $user->school_id ?? null;

        $data = $this->collectReportData($startDate, $endDate, $schoolId);

        if (! $useAi) {
            return [
                'success' => true,
                'report' => $this->generateStandardReport($data),
                'data' => $data,
                'usage' => null,
                'type' => 'standard',
            ];
        }

        $prompt = $this->buildReportPrompt($data, $startDate, $endDate);

        $response = $this->chatGPTService->chat($prompt, 'gpt-4.1-nano', [
            'request_type' => 'performance_report',
            'temperature' => 0.7,
        ]);

        if (! $response['success']) {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Failed to generate report',
            ];
        }

        return [
            'success' => true,
            'report' => $response['content'],
            'data' => $data,
            'usage' => $response['usage'] ?? null,
            'type' => 'ai',
        ];
    }

    private function collectReportData($startDate, $endDate, $schoolId = null): array
    {
        $userId = auth()->id();

        $examsQuery = GeneralExam::query()
            ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->where('user_id', $userId)
            ->with(['submissions', 'configuredParticipants', 'subscription.subjects'])
            ->withCount('configuredParticipants');

        $exams = $examsQuery->get();

        $submissionsQuery = GeneralExamSubmission::whereHas('assignment', function ($query) use ($startDate, $endDate, $userId) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
                ->where('user_id', $userId);
        })->whereNotNull('submitted_at');

        $submissions = $submissionsQuery->with('assignment.subscription.subjects')->get();

        // Overall statistics
        $totalExams = $exams->count();
        $totalSubmissions = $submissions->count();
        $averageScore = round($submissions->avg('percentage'), 2);

        // Calculate completion rate based on configured participants or actual submissions
        $totalConfigured = $exams->sum('configured_participants_count');
        $completionRate = $totalConfigured > 0
            ? round(($totalSubmissions / $totalConfigured) * 100, 2)
            : ($totalExams > 0 ? round(($totalSubmissions / $totalExams) * 100, 2) : 0);

        // Grade distribution
        $gradeDistribution = $submissions->groupBy('grade')->map->count()->toArray();

        // Subject performance
        $subjectPerformance = $submissions->groupBy(function ($submission) {
            $subjects = $submission->assignment?->subscription?->subjects;

            return $subjects && $subjects->isNotEmpty()
                ? $subjects->pluck('name')->join(', ')
                : 'Unspecified';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'average_score' => round($group->avg('percentage'), 2),
                'highest_score' => round($group->max('percentage'), 2),
                'lowest_score' => round($group->min('percentage'), 2),
            ];
        })->toArray();

        // Configured vs actual turnout
        $configuredVsActual = $exams->map(function ($exam) {
            $configured = $exam->configuredParticipants()->count();
            $actual = $exam->submissions()->whereNotNull('submitted_at')->count();

            // If no configured participants, use actual submissions as the participant count
            if ($configured === 0) {
                $configured = $actual;
            }

            return [
                'exam_title' => $exam->title,
                'configured' => $configured,
                'actual' => $actual,
                'turnout_rate' => $configured > 0 ? round(($actual / $configured) * 100, 2) : 0,
            ];
        })->toArray();

        // Time-based trends
        $dailySubmissions = $submissions->groupBy(function ($submission) {
            return $submission->submitted_at->format('Y-m-d');
        })->map->count()->toArray();

        // Performance by exam
        $examPerformance = $exams->map(function ($exam) {
            $examSubmissions = $exam->submissions()->whereNotNull('submitted_at')->get();

            return [
                'title' => $exam->title,
                'submissions' => $examSubmissions->count(),
                'average_score' => round($examSubmissions->avg('percentage'), 2),
                'pass_rate' => round($examSubmissions->where('percentage', '>=', 50)->count() / max($examSubmissions->count(), 1) * 100, 2),
            ];
        })->toArray();

        // Top and bottom performers
        $topPerformers = $submissions->sortByDesc('percentage')->take(5)->map(function ($submission) {
            return [
                'name' => $submission->participant_name,
                'exam' => $submission->assignment->title,
                'score' => $submission->percentage,
                'grade' => $submission->grade,
            ];
        })->values()->toArray();

        $bottomPerformers = $submissions->sortBy('percentage')->take(5)->map(function ($submission) {
            return [
                'name' => $submission->participant_name,
                'exam' => $submission->assignment->title,
                'score' => $submission->percentage,
                'grade' => $submission->grade,
            ];
        })->values()->toArray();

        // Question-level statistics
        $questionStats = $this->collectQuestionStatistics($exams, $submissions);

        return [
            'period' => [
                'start' => Carbon::parse($startDate)->format('Y-m-d'),
                'end' => Carbon::parse($endDate)->format('Y-m-d'),
            ],
            'overview' => [
                'total_exams' => $totalExams,
                'total_submissions' => $totalSubmissions,
                'average_score' => $averageScore,
                'completion_rate' => $completionRate,
            ],
            'grade_distribution' => $gradeDistribution,
            'subject_performance' => $subjectPerformance,
            'configured_vs_actual' => $configuredVsActual,
            'daily_submissions' => $dailySubmissions,
            'exam_performance' => $examPerformance,
            'top_performers' => $topPerformers,
            'bottom_performers' => $bottomPerformers,
            'question_statistics' => $questionStats,
        ];
    }

    private function collectQuestionStatistics($exams, $submissions): array
    {
        $questionData = [];

        foreach ($exams as $exam) {
            $questions = $exam->questions;
            $examSubmissions = $submissions->where('general_exam_id', $exam->id);

            foreach ($questions as $question) {
                $totalAttempts = 0;
                $correctCount = 0;
                $incorrectCount = 0;
                $unansweredCount = 0;
                $totalPointsEarned = 0;

                foreach ($examSubmissions as $submission) {
                    $totalAttempts++;
                    $response = $submission->responses[$question->id] ?? null;

                    if ($response === null || ! isset($response['response']) || $response['response'] === null) {
                        $unansweredCount++;
                    } else {
                        $isCorrect = $response['is_correct'] ?? false;
                        if ($isCorrect) {
                            $correctCount++;
                        } else {
                            $incorrectCount++;
                        }
                        $totalPointsEarned += $response['points_earned'] ?? 0;
                    }
                }

                if ($totalAttempts > 0) {
                    $questionData[] = [
                        'exam_title' => $exam->title,
                        'question_text' => substr(strip_tags($question->question), 0, 100),
                        'question_type' => $question->type,
                        'marks' => $question->marks,
                        'difficulty' => $question->difficulty,
                        'total_attempts' => $totalAttempts,
                        'correct_count' => $correctCount,
                        'incorrect_count' => $incorrectCount,
                        'unanswered_count' => $unansweredCount,
                        'correct_rate' => round(($correctCount / $totalAttempts) * 100, 2),
                        'incorrect_rate' => round(($incorrectCount / $totalAttempts) * 100, 2),
                        'unanswered_rate' => round(($unansweredCount / $totalAttempts) * 100, 2),
                        'average_points' => $totalAttempts > 0 ? round($totalPointsEarned / $totalAttempts, 2) : 0,
                    ];
                }
            }
        }

        $collection = collect($questionData);

        return [
            'most_answered' => $collection->sortByDesc('correct_count')->take(10)->values()->toArray(),
            'most_incorrect' => $collection->sortByDesc('incorrect_count')->take(10)->values()->toArray(),
            'most_unanswered' => $collection->sortByDesc('unanswered_count')->take(10)->values()->toArray(),
            'highest_correct_rate' => $collection->sortByDesc('correct_rate')->take(10)->values()->toArray(),
            'lowest_correct_rate' => $collection->where('total_attempts', '>=', 3)->sortBy('correct_rate')->take(10)->values()->toArray(),
        ];
    }

    private function buildReportPrompt(array $data, $startDate, $endDate): string
    {
        $dataJson = json_encode($data, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are an educational data analyst. Generate a well-structured performance report using markdown formatting.

**Report Period:** {$data['period']['start']} to {$data['period']['end']}

**Data:**
```json
{$dataJson}
```

**Required Structure:**

# Executive Summary
- Brief overview with key metrics
- 2-3 critical highlights
- 1-2 areas of concern

# Exam Statistics
| Metric | Value |
|:-------|:------|
| Total Exams | [number] |
| Total Submissions | [number] |
| Average Score | [percentage] |
| Completion Rate | [percentage] |

# Subject Performance
| Subject | Submissions | Avg Score | Highest | Lowest |
|:--------|:------------|:----------|:--------|:-------|
[table rows]

**Analysis:** Brief insights on subject trends

# Grade Distribution
| Grade | Count | Percentage |
|:------|:------|:-----------|
[table rows]

**Analysis:** Brief interpretation

# Participation Analysis
| Exam | Configured | Actual | Turnout Rate |
|:-----|:-----------|:-------|:-------------|
[table rows]

**Note:** For exams without participant restrictions, configured count equals actual submissions.
**Key Findings:** Highlight low/high turnout exams

# Daily Submission Trends
| Date | Submissions |
|:-----|:------------|
[table rows]

**Pattern Analysis:** Peak periods and trends

# Individual Exam Performance
| Exam | Submissions | Avg Score | Pass Rate |
|:-----|:------------|:----------|:----------|
[table rows]

**Insights:** Best and challenging exams

# Top Performers
| Rank | Name | Exam | Score | Grade |
|:-----|:-----|:-----|:------|:------|
[top 5]

# Bottom Performers
| Rank | Name | Exam | Score | Grade |
|:-----|:-----|:-----|:------|:------|
[bottom 5]

# Recommendations
1. **[Category]:** Specific actionable recommendation
2. **[Category]:** Specific actionable recommendation
3. **[Category]:** Specific actionable recommendation

# Question-Level Analysis

## Most Correctly Answered Questions
| Exam | Question | Type | Correct Rate | Attempts |
|:-----|:---------|:-----|:-------------|:---------|
[top 10]

## Most Incorrectly Answered Questions
| Exam | Question | Type | Incorrect Rate | Attempts |
|:-----|:---------|:-----|:---------------|:---------|
[top 10]

## Most Unanswered Questions
| Exam | Question | Type | Unanswered Rate | Attempts |
|:-----|:---------|:-----|:----------------|:---------|
[top 10]

**Insights:** Identify patterns in difficult questions and topics requiring additional instruction

# Recommendations
1. **[Category]:** Specific actionable recommendation
2. **[Category]:** Specific actionable recommendation
3. **[Category]:** Specific actionable recommendation

# Suggested Visualizations
- **Chart 1:** [Description]
- **Chart 2:** [Description]
- **Chart 3:** [Description]

**CRITICAL:** Use markdown tables with left-aligned columns (`:------`). Keep analysis concise. Focus on actionable insights.
PROMPT;
    }

    private function generateStandardReport(array $data): string
    {
        $report = "# Examination Performance Report\n\n";
        $report .= "**Report Period:** {$data['period']['start']} to {$data['period']['end']}\n\n";

        // Executive Summary
        $report .= "## Executive Summary\n\n";
        $report .= 'This report provides a comprehensive analysis of examination performance for the specified period. ';
        $report .= "A total of **{$data['overview']['total_exams']} exams** were conducted with **{$data['overview']['total_submissions']} submissions** received.\n\n";

        $avgScore = $data['overview']['average_score'];
        $completionRate = $data['overview']['completion_rate'];

        $report .= "### Key Highlights\n\n";
        $report .= "- **Average Score:** {$avgScore}% ";
        $report .= $avgScore >= 70 ? '(Excellent performance)' : ($avgScore >= 50 ? '(Satisfactory performance)' : '(Needs improvement)');
        $report .= "\n";
        $report .= "- **Completion Rate:** {$completionRate}%";
        $report .= $completionRate >= 80 ? ' (High engagement)' : ($completionRate >= 50 ? ' (Moderate engagement)' : ' (Low engagement)');
        $report .= "\n";
        $report .= "- **Total Participants:** {$data['overview']['total_submissions']}\n\n";

        // Exam Statistics
        $report .= "## Exam Statistics\n\n";
        $report .= "| Metric | Value |\n";
        $report .= "|:-------|:------|\n";
        $report .= "| Total Exams | {$data['overview']['total_exams']} |\n";
        $report .= "| Total Submissions | {$data['overview']['total_submissions']} |\n";
        $report .= "| Average Score | {$data['overview']['average_score']}% |\n";
        $report .= "| Completion Rate | {$data['overview']['completion_rate']}% |\n\n";

        // Subject Performance
        if (! empty($data['subject_performance'])) {
            $report .= "## Subject Performance\n\n";
            $report .= "| Subject | Submissions | Avg Score | Highest | Lowest |\n";
            $report .= "|:--------|:------------|:----------|:--------|:-------|\n";

            foreach ($data['subject_performance'] as $subject => $stats) {
                $report .= "| {$subject} | {$stats['count']} | {$stats['average_score']}% | {$stats['highest_score']}% | {$stats['lowest_score']}% |\n";
            }

            $report .= "\n**Analysis:** ";
            $bestSubject = collect($data['subject_performance'])->sortByDesc('average_score')->keys()->first();
            $worstSubject = collect($data['subject_performance'])->sortBy('average_score')->keys()->first();
            $report .= "Best performing subject is **{$bestSubject}**. ";
            if ($bestSubject !== $worstSubject) {
                $report .= "**{$worstSubject}** requires additional attention and support.";
            }
            $report .= "\n\n";
        }

        // Grade Distribution
        if (! empty($data['grade_distribution'])) {
            $report .= "## Grade Distribution\n\n";
            $report .= "| Grade | Count | Percentage |\n";
            $report .= "|:------|:------|:-----------|\n";

            $totalGrades = array_sum($data['grade_distribution']);
            foreach ($data['grade_distribution'] as $grade => $count) {
                $percentage = $totalGrades > 0 ? round(($count / $totalGrades) * 100, 2) : 0;
                $report .= "| {$grade} | {$count} | {$percentage}% |\n";
            }

            $report .= "\n**Analysis:** ";
            $topGrade = collect($data['grade_distribution'])->sortByDesc(fn ($count) => $count)->keys()->first();
            $report .= "Most common grade is **{$topGrade}** with ".$data['grade_distribution'][$topGrade]." students.\n\n";
        }

        // Participation Analysis
        if (! empty($data['configured_vs_actual'])) {
            $report .= "## Participation Analysis\n\n";
            $report .= "| Exam | Configured | Actual | Turnout Rate |\n";
            $report .= "|:-----|:-----------|:-------|:-------------|\n";

            foreach ($data['configured_vs_actual'] as $exam) {
                $report .= "| {$exam['exam_title']} | {$exam['configured']} | {$exam['actual']} | {$exam['turnout_rate']}% |\n";
            }

            $report .= "\n**Note:** For exams without participant restrictions, configured count equals actual submissions.\n\n";

            $lowTurnout = collect($data['configured_vs_actual'])->where('turnout_rate', '<', 50)->pluck('exam_title');
            if ($lowTurnout->isNotEmpty()) {
                $report .= '**Key Findings:** Low turnout detected in: '.$lowTurnout->join(', ')."\n\n";
            }
        }

        // Daily Submission Trends
        if (! empty($data['daily_submissions'])) {
            $report .= "## Daily Submission Trends\n\n";
            $report .= "| Date | Submissions |\n";
            $report .= "|:-----|:------------|\n";

            foreach ($data['daily_submissions'] as $date => $count) {
                $report .= "| {$date} | {$count} |\n";
            }

            $peakDay = collect($data['daily_submissions'])->sortByDesc(fn ($count) => $count)->keys()->first();
            $peakCount = $data['daily_submissions'][$peakDay];
            $report .= "\n**Pattern Analysis:** Peak submission day was **{$peakDay}** with {$peakCount} submissions.\n\n";
        }

        // Individual Exam Performance
        if (! empty($data['exam_performance'])) {
            $report .= "## Individual Exam Performance\n\n";
            $report .= "| Exam | Submissions | Avg Score | Pass Rate |\n";
            $report .= "|:-----|:------------|:----------|:----------|\n";

            foreach ($data['exam_performance'] as $exam) {
                $report .= "| {$exam['title']} | {$exam['submissions']} | {$exam['average_score']}% | {$exam['pass_rate']}% |\n";
            }

            $bestExam = collect($data['exam_performance'])->sortByDesc('average_score')->first();
            $challengingExam = collect($data['exam_performance'])->sortBy('average_score')->first();

            $report .= "\n**Insights:** ";
            $report .= "Best performing exam: **{$bestExam['title']}** ({$bestExam['average_score']}%). ";
            if ($bestExam['title'] !== $challengingExam['title']) {
                $report .= "Most challenging: **{$challengingExam['title']}** ({$challengingExam['average_score']}%).";
            }
            $report .= "\n\n";
        }

        // Top Performers
        if (! empty($data['top_performers'])) {
            $report .= "## Top Performers\n\n";
            $report .= "| Rank | Name | Exam | Score | Grade |\n";
            $report .= "|:-----|:-----|:-----|:------|:------|\n";

            foreach ($data['top_performers'] as $index => $performer) {
                $rank = $index + 1;
                $report .= "| {$rank} | {$performer['name']} | {$performer['exam']} | {$performer['score']}% | {$performer['grade']} |\n";
            }
            $report .= "\n";
        }

        // Bottom Performers
        if (! empty($data['bottom_performers'])) {
            $report .= "## Bottom Performers\n\n";
            $report .= "| Rank | Name | Exam | Score | Grade |\n";
            $report .= "|:-----|:-----|:-----|:------|:------|\n";

            foreach ($data['bottom_performers'] as $index => $performer) {
                $rank = $index + 1;
                $report .= "| {$rank} | {$performer['name']} | {$performer['exam']} | {$performer['score']}% | {$performer['grade']} |\n";
            }
            $report .= "\n";
        }

        // Recommendations
        $report .= "## Recommendations\n\n";

        if ($avgScore < 50) {
            $report .= "1. **Academic Support:** Consider implementing additional tutoring sessions or study groups to improve overall performance.\n";
        } elseif ($avgScore < 70) {
            $report .= "1. **Performance Enhancement:** Focus on targeted interventions for students scoring below 50% to raise the overall average.\n";
        } else {
            $report .= "1. **Maintain Excellence:** Continue current teaching methods while identifying opportunities for advanced learning.\n";
        }

        if ($completionRate < 70) {
            $report .= "2. **Engagement:** Improve exam participation through better communication and scheduling.\n";
        }

        if (! empty($data['bottom_performers'])) {
            $report .= "3. **Intervention:** Provide personalized support for bottom performers to help them improve.\n";
        }

        // Question-Level Analysis
        if (! empty($data['question_statistics'])) {
            $report .= "## Question-Level Analysis\n\n";

            // Most Correctly Answered
            if (! empty($data['question_statistics']['most_answered'])) {
                $report .= "### Most Correctly Answered Questions\n\n";
                $report .= "| Exam | Question | Type | Correct Rate | Attempts |\n";
                $report .= "|:-----|:---------|:-----|:-------------|:---------|\n";

                foreach (array_slice($data['question_statistics']['most_answered'], 0, 10) as $q) {
                    $questionPreview = strlen($q['question_text']) > 80 ? substr($q['question_text'], 0, 80).'...' : $q['question_text'];
                    $report .= "| {$q['exam_title']} | {$questionPreview} | {$q['question_type']} | {$q['correct_rate']}% | {$q['total_attempts']} |\n";
                }
                $report .= "\n";
            }

            // Most Incorrectly Answered
            if (! empty($data['question_statistics']['most_incorrect'])) {
                $report .= "### Most Incorrectly Answered Questions\n\n";
                $report .= "| Exam | Question | Type | Incorrect Rate | Attempts |\n";
                $report .= "|:-----|:---------|:-----|:---------------|:---------|\n";

                foreach (array_slice($data['question_statistics']['most_incorrect'], 0, 10) as $q) {
                    $questionPreview = strlen($q['question_text']) > 80 ? substr($q['question_text'], 0, 80).'...' : $q['question_text'];
                    $report .= "| {$q['exam_title']} | {$questionPreview} | {$q['question_type']} | {$q['incorrect_rate']}% | {$q['total_attempts']} |\n";
                }
                $report .= "\n**Insights:** These questions represent areas where students struggled most. Consider reviewing these topics in class.\n\n";
            }

            // Most Unanswered
            if (! empty($data['question_statistics']['most_unanswered'])) {
                $report .= "### Most Unanswered Questions\n\n";
                $report .= "| Exam | Question | Type | Unanswered Rate | Attempts |\n";
                $report .= "|:-----|:---------|:-----|:----------------|:---------|\n";

                foreach (array_slice($data['question_statistics']['most_unanswered'], 0, 10) as $q) {
                    $questionPreview = strlen($q['question_text']) > 80 ? substr($q['question_text'], 0, 80).'...' : $q['question_text'];
                    $report .= "| {$q['exam_title']} | {$questionPreview} | {$q['question_type']} | {$q['unanswered_rate']}% | {$q['total_attempts']} |\n";
                }
                $report .= "\n**Insights:** High unanswered rates may indicate time constraints, unclear questions, or difficulty level issues.\n\n";
            }

            // Lowest Correct Rate (Most Challenging)
            if (! empty($data['question_statistics']['lowest_correct_rate'])) {
                $report .= "### Most Challenging Questions (Lowest Correct Rate)\n\n";
                $report .= "| Exam | Question | Type | Correct Rate | Attempts |\n";
                $report .= "|:-----|:---------|:-----|:-------------|:---------|\n";

                foreach (array_slice($data['question_statistics']['lowest_correct_rate'], 0, 10) as $q) {
                    $questionPreview = strlen($q['question_text']) > 80 ? substr($q['question_text'], 0, 80).'...' : $q['question_text'];
                    $report .= "| {$q['exam_title']} | {$questionPreview} | {$q['question_type']} | {$q['correct_rate']}% | {$q['total_attempts']} |\n";
                }
                $report .= "\n**Insights:** These questions had the lowest success rates and may need revision or additional teaching focus.\n\n";
            }
        }

        $report .= "## Suggested Visualizations\n\n";
        $report .= "- **Chart 1:** Grade distribution pie chart showing percentage of students in each grade category\n";
        $report .= "- **Chart 2:** Subject performance bar chart comparing average scores across subjects\n";
        $report .= "- **Chart 3:** Daily submission trend line graph showing engagement patterns over time\n";
        $report .= "- **Chart 4:** Question difficulty heatmap showing correct rates by question type and difficulty\n";

        return $report;
    }
}
