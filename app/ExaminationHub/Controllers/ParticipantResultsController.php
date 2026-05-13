<?php

namespace App\ExaminationHub\Controllers;

use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantResultsController extends Controller
{
    private const RESULT_ACCESS_SESSION_KEY = 'exam_results_access';

    public function index(Request $request): View
    {
        $email = $request->query('email');

        if (!$email) {
            return view('examination-hub.results.index', [
                'needsEmail' => true,
                'submissions' => collect(),
                'summary' => [],
            ]);
        }

        $emailCheck = $this->verifyEmail($request, $email);
        if ($emailCheck instanceof RedirectResponse) {
            return view('examination-hub.results.index', [
                'needsEmail' => true,
                'submissions' => collect(),
                'summary' => [],
            ]);
        }

        $submissions = GeneralExamSubmission::query()
            ->where('participant_email', strtolower($email))
            ->whereNotNull('submitted_at')
            ->with(['assignment.user'])
            ->orderByDesc('submitted_at')
            ->get();

        $summary = [
            'total_submissions' => $submissions->count(),
            'results_released' => $submissions->filter(fn($s) => $s->canViewResults())->count(),
            'average_percentage' => round((float) $submissions->avg('percentage'), 2),
            'best_percentage' => round((float) $submissions->max('percentage'), 2),
        ];

        $performanceTrend = $submissions
            ->sortBy('submitted_at')
            ->take(10)
            ->mapWithKeys(function ($submission) {
                $date = $submission->submitted_at?->format('M d') ?? 'N/A';
                return [$date => round((float) ($submission->percentage ?? 0), 1)];
            });

        $gradeDistribution = $submissions->groupBy(function ($submission) {
            $percentage = $submission->percentage ?? 0;
            return match (true) {
                $percentage >= 90 => 'A+',
                $percentage >= 80 => 'A',
                $percentage >= 70 => 'B',
                $percentage >= 60 => 'C',
                $percentage >= 50 => 'D',
                default => 'F',
            };
        })->map->count();

        return view('examination-hub.results.index', [
            'needsEmail' => false,
            'email' => $email,
            'submissions' => $submissions,
            'summary' => $summary,
            'performanceTrend' => $performanceTrend,
            'gradeDistribution' => $gradeDistribution,
        ]);
    }

    public function show(Request $request, GeneralExamSubmission $submission): View|RedirectResponse
    {
        $email = $request->query('email') ?? session(self::RESULT_ACCESS_SESSION_KEY);

        if (!$email || strtolower($submission->participant_email) !== strtolower($email)) {
            return redirect()->route('examination-hub.results.index')
                ->withErrors(['error' => 'Unauthorized access.']);
        }

        return view('examination-hub.results.show', [
            'submission' => $submission->load('assignment'),
            'email' => $email,
        ]);
    }

    private function verifyEmail(Request $request, string $email): ?RedirectResponse
    {
        if ($this->hasResultAccessSession($request, $email)) {
            return null;
        }

        $exists = GeneralExamSubmission::where('participant_email', strtolower($email))
            ->whereNotNull('submitted_at')
            ->exists();

        if (!$exists) {
            return redirect()->back()->with('error', 'No results found for this email.');
        }

        $request->session()->put(self::RESULT_ACCESS_SESSION_KEY, strtolower($email));

        return null;
    }

    private function hasResultAccessSession(Request $request, string $email): bool
    {
        $sessionEmail = $request->session()->get(self::RESULT_ACCESS_SESSION_KEY);
        return $sessionEmail && strtolower($sessionEmail) === strtolower($email);
    }
}
