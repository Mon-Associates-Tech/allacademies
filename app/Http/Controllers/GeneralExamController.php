<?php

namespace App\Http\Controllers;

use App\Models\GeneralExamParticipant;
use App\Models\GeneralExamSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralExamController extends Controller
{
    private const RESULT_ACCESS_SESSION_KEY = 'general_exam_results_access';

    /**
     * Show the join assignment page.
     */
    public function join(?string $code = null): View
    {
        return view('public.general-exams.join', [
            'code' => $code,
        ]);
    }

    /**
     * Show the take assignment page.
     */
    public function take(GeneralExamSubmission $submission): View
    {
        return view('public.general-exams.take', [
            'submission' => $submission,
        ]);
    }

    /**
     * Show the assignment results page.
     */
    public function results(Request $request, ?string $token = null): View|RedirectResponse
    {
        $token = $token ?: $request->query('token');

        if (! $token) {
            return view('public.general-exams.results-dashboard', [
                'token' => null,
                'participant' => null,
                'needsEmail' => false,
                'submissions' => collect(),
                'summary' => [],
                'subjectOptions' => [],
                'assignerOptions' => [],
                'selectedSubject' => null,
                'selectedAssigner' => null,
            ]);
        }

        return redirect()->route('general-exams.results.dashboard', ['token' => $token] + $request->only('email'));
    }

    public function dashboard(Request $request, string $token): View|RedirectResponse
    {
        $participant = GeneralExamParticipant::findByResultToken($token);

        if (! $participant) {
            return view('public.general-exams.results-dashboard', [
                'token' => $token,
                'participant' => null,
                'needsEmail' => false,
                'submissions' => collect(),
                'summary' => [],
                'subjectOptions' => [],
                'assignerOptions' => [],
                'selectedSubject' => null,
                'selectedAssigner' => null,
            ]);
        }

        $emailCheck = $this->ensureResultAccess($request, $participant, $token);
        if ($emailCheck instanceof RedirectResponse) {
            return $emailCheck;
        }
        $needsEmail = ! $this->hasResultAccessSession($request, $token, $participant);

        $subjectFilter = trim((string) $request->query('subject', ''));
        $assignerFilter = trim((string) $request->query('assigner', ''));

        $baseQuery = GeneralExamSubmission::query()
            ->where('participant_id', $participant->id)
            ->whereIn('participant_type', [GeneralExamParticipant::class, 'participant'])
            ->whereNotNull('submitted_at')
            ->with([
                'assignment.user',
                'assignment.subscription.subjects',
            ])
            ->orderByDesc('submitted_at')
            ->orderByDesc('id');

        $submissions = (clone $baseQuery)
            ->when($subjectFilter !== '', function (Builder $query) use ($subjectFilter) {
                $query->whereHas('assignment.subscription.subjects', function (Builder $subjectQuery) use ($subjectFilter) {
                    $subjectQuery->where('academic_subjects.id', $subjectFilter);
                });
            })
            ->when($assignerFilter !== '', function (Builder $query) use ($assignerFilter) {
                $query->whereHas('assignment.user', function (Builder $userQuery) use ($assignerFilter) {
                    $userQuery->where('id', $assignerFilter);
                });
            })
            ->get();

        $allSubmissions = $baseQuery->get();

        $summary = [
            'total_submissions' => $submissions->count(),
            'results_released' => $submissions->filter(fn ($submission) => $submission->canViewResults())->count(),
            'average_percentage' => round((float) $submissions->avg('percentage'), 2),
            'best_percentage' => round((float) $submissions->max('percentage'), 2),
        ];

        $subjectOptions = $allSubmissions
            ->flatMap(function ($submission) {
                return optional($submission->assignment?->subscription)->subjects ?? collect();
            })
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(fn ($subject) => ['id' => (string) $subject->id, 'name' => $subject->name]);

        $assignerOptions = $allSubmissions
            ->map(fn ($submission) => $submission->assignment?->user)
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(fn ($assigner) => ['id' => (string) $assigner->id, 'name' => $assigner->name]);

        return view('public.general-exams.results-dashboard', [
            'token' => $token,
            'participant' => $participant,
            'needsEmail' => $needsEmail,
            'submissions' => $submissions,
            'summary' => $summary,
            'subjectOptions' => $subjectOptions,
            'assignerOptions' => $assignerOptions,
            'selectedSubject' => $subjectFilter !== '' ? $subjectFilter : null,
            'selectedAssigner' => $assignerFilter !== '' ? $assignerFilter : null,
        ]);
    }

    public function submissionResult(Request $request, string $token, GeneralExamSubmission $submission): View|RedirectResponse
    {
        $participant = GeneralExamParticipant::findByResultToken($token);
        if (! $participant) {
            return redirect()->route('general-exams.join')->with('error', 'Invalid result link.');
        }

        $emailCheck = $this->ensureResultAccess($request, $participant, $token);
        if ($emailCheck instanceof RedirectResponse) {
            return $emailCheck;
        }
        if (! $this->hasResultAccessSession($request, $token, $participant)) {
            return redirect()->route('general-exams.results.dashboard', ['token' => $token]);
        }

        if (
            ! in_array($submission->participant_type, [GeneralExamParticipant::class, 'participant'], true)
            || (int) $submission->participant_id !== (int) $participant->id
        ) {
            abort(403, 'You do not have permission to view this result.');
        }

        return view('public.general-exams.results', [
            'token' => $token,
            'submission' => $submission->load('assignment'),
            'needsEmail' => false,
        ]);
    }

    private function ensureResultAccess(Request $request, GeneralExamParticipant $participant, string $token): ?RedirectResponse
    {
        if ($this->hasResultAccessSession($request, $token, $participant)) {
            return null;
        }

        $email = strtolower(trim((string) ($request->input('email') ?? $request->query('email'))));
        if ($email === '') {
            return null;
        }

        if ($email !== strtolower($participant->email)) {
            return redirect()->back()->with('error', 'Email does not match this result link.');
        }

        $session = $request->session()->get(self::RESULT_ACCESS_SESSION_KEY, []);
        $session[$token] = [
            'participant_id' => $participant->id,
            'verified_at' => now()->toISOString(),
        ];
        $request->session()->put(self::RESULT_ACCESS_SESSION_KEY, $session);

        return redirect()->route('general-exams.results.dashboard', ['token' => $token]);
    }

    private function hasResultAccessSession(Request $request, string $token, GeneralExamParticipant $participant): bool
    {
        $session = $request->session()->get(self::RESULT_ACCESS_SESSION_KEY, []);
        $entry = $session[$token] ?? null;

        return is_array($entry) && ((int) ($entry['participant_id'] ?? 0) === (int) $participant->id);
    }
}
