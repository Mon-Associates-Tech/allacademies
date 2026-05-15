<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Services\GradingSystemService;
use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamTakingController extends Controller
{
    private $gradingService;

    public function __construct()
    {
        $this->gradingService = app()->make(GradingSystemService::class);
    }

    public function join(): View
    {
        return view('examination-hub.take.join');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'access_code' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:255'],
        ]);

        $exam = GeneralExam::findByAccessCode($data['access_code']);

        if (!$exam) {
            return back()->withErrors(['access_code' => 'Invalid access code.']);
        }

        if (!$exam->isActive()) {
            return back()->withErrors(['access_code' => 'This examination is not currently active.']);
        }

        $participantData = $this->validateParticipant($exam, $data);

        if (isset($participantData['error'])) {
            return back()->withErrors(['access_code' => $participantData['error']]);
        }

        $submission = $this->getOrCreateSubmission($exam, $participantData);

        if (!$submission) {
            return back()->withErrors(['access_code' => 'You have reached the maximum number of attempts.']);
        }

        session([
            'exam_submission_id' => $submission->id,
            'exam_participant_data' => $participantData,
        ]);

        return redirect()->route('examination-hub.take.start', $exam);
    }

    public function start(GeneralExam $exam): View|RedirectResponse
    {
        $submissionId = session('exam_submission_id');
        $submission = GeneralExamSubmission::find($submissionId);

        if (!$submission || $submission->general_exam_id !== $exam->id) {
            return redirect()->route('examination-hub.take.join')
                ->withErrors(['error' => 'Invalid session. Please join again.']);
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        $exam->load(['sections' => function ($query) {
            $query->orderBy('order')->withCount('questions');
        }]);

        return view('examination-hub.take.start', [
            'exam' => $exam,
            'submission' => $submission,
        ]);
    }

    public function section(GeneralExam $exam, int $sectionIndex): View|RedirectResponse
    {
        $submissionId = session('exam_submission_id');
        $submission = GeneralExamSubmission::find($submissionId);

        if (!$submission || $submission->general_exam_id !== $exam->id) {
            return redirect()->route('examination-hub.take.join')
                ->withErrors(['error' => 'Invalid session. Please join again.']);
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        $exam->load(['sections.questions' => function ($query) {
            $query->orderBy('order');
        }]);

        $section = $exam->sections->get($sectionIndex);

        if (!$section) {
            abort(404, 'Section not found.');
        }

        $questions = $section->questions;

        // Handle randomization per participant
        if ($section->is_randomized && $questions->isNotEmpty()) {
            $randomizedOrder = $submission->randomized_question_order ?? [];
            $sectionKey = "section_{$section->id}";

            // If this section hasn't been randomized for this participant yet, randomize and store
            if (!isset($randomizedOrder[$sectionKey])) {
                $questionIds = $questions->pluck('id')->shuffle()->values()->toArray();
                $randomizedOrder[$sectionKey] = $questionIds;
                $submission->update(['randomized_question_order' => $randomizedOrder]);
            }

            // Reorder questions based on stored randomized order
            $orderedQuestions = collect();
            foreach ($randomizedOrder[$sectionKey] as $questionId) {
                $question = $questions->firstWhere('id', $questionId);
                if ($question) {
                    $orderedQuestions->push($question);
                }
            }
            $questions = $orderedQuestions;
        }

        $responses = $submission->responses ?? [];

        return view('examination-hub.take.section', [
            'exam' => $exam,
            'submission' => $submission,
            'section' => $section,
            'sectionIndex' => $sectionIndex,
            'questions' => $questions,
            'responses' => $responses,
        ]);
    }

    public function saveResponse(Request $request, GeneralExam $exam): JsonResponse|RedirectResponse
    {
        $submissionId = session('exam_submission_id');
        $submission   = GeneralExamSubmission::find($submissionId);

        if (!$submission || $submission->general_exam_id !== $exam->id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invalid session.'], 403);
            }
            abort(403, 'Invalid session.');
        }

        if ($submission->submitted_at) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'already_submitted']);
            }
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        $data = $request->validate([
            'question_id'  => ['required', 'integer', 'exists:general_exam_questions,id'],
            'response'     => ['required', 'string'],
            'section_index' => ['required', 'integer'],
        ]);

        $responses = $submission->responses ?? [];
        $responses[$data['question_id']] = [
            'response'    => $data['response'],
            'answered_at' => now()->toIso8601String(),
        ];

        $submission->update(['responses' => $responses]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'saved', 'question_id' => $data['question_id']]);
        }

        return back()->with('success', 'Response saved.');
    }
    public function submit(Request $request, GeneralExam $exam): RedirectResponse
    {
        $submissionId = session('exam_submission_id');
        $submission = GeneralExamSubmission::find($submissionId);

        if (!$submission || $submission->general_exam_id !== $exam->id) {
            abort(403, 'Invalid session.');
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        DB::transaction(function () use ($submission, $exam) {
            $timeTaken = $submission->started_at
                ? $submission->started_at->diffInMinutes(now())
                : 0;

            $submission->update([
                'submitted_at' => now(),
                'time_taken_minutes' => $timeTaken,
            ]);

            $this->autoGradeSubmission($submission, $exam);
        });

        session()->forget(['exam_submission_id', 'exam_participant_data']);

        return redirect()->route('examination-hub.take.completed', $exam);
    }

    public function completed(GeneralExam $exam): View
    {
        $participantEmail = session('exam_participant_data.email');

        return view('examination-hub.take.completed', [
            'exam' => $exam,
            'participantEmail' => $participantEmail,
        ]);
    }

    private function validateParticipant(GeneralExam $exam, array $data): array
    {
        $mode = $exam->participant_mode;
        $requiredFields = $exam->participant_required_fields ?? [];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return ['error' => "The {$field} field is required."];
            }
        }

        if (in_array($mode, ['configured', 'both'])) {
            $matchMode = $exam->configured_match_mode ?? 'any';
            $email = $data['email'] ?? null;
            $code = $data['unique_code'] ?? null;

            $query = $exam->configuredParticipants()->where('is_active', true);

            if ($matchMode === 'both') {
                $query->where('email', $email)->where('unique_code', $code);
            } else {
                $query->where(function ($q) use ($email, $code) {
                    if ($email) {
                        $q->orWhere('email', $email);
                    }
                    if ($code) {
                        $q->orWhere('unique_code', $code);
                    }
                });
            }

            $configured = $query->first();

            if ($mode === 'configured' && !$configured) {
                return ['error' => 'You are not authorized to take this examination.'];
            }

            if ($configured) {
                return [
                    'type' => 'configured',
                    'id' => $configured->id,
                    'name' => $data['name'] ?? $configured->name,
                    'email' => $data['email'] ?? $configured->email,
                ];
            }
        }

        return [
            'type' => 'general',
            'id' => null,
            'name' => $data['name'] ?? 'Anonymous',
            'email' => $data['email'] ?? null,
        ];
    }

    private function getOrCreateSubmission(GeneralExam $exam, array $participantData): ?GeneralExamSubmission
    {
        $participantType = $participantData['type'];
        $participantId = $participantData['id'];

        // For general participants, generate a unique numeric ID from email
        if ($participantType === 'general' && !empty($participantData['email'])) {
            // Use CRC32 to convert email to a numeric ID (always positive)
            $participantId = abs(crc32($participantData['email']));
        }

        if (!$exam->canParticipantAttempt($participantType, $participantId)) {
            return null;
        }

        return GeneralExamSubmission::firstOrCreate(
            [
                'general_exam_id' => $exam->id,
                'participant_type' => $participantType,
                'participant_id' => $participantId,
                'submitted_at' => null,
            ],
            [
                'participant_name' => $participantData['name'],
                'participant_email' => $participantData['email'],
                'started_at' => now(),
                'responses' => [],
                'score' => 0,
                'status' => 'not_started',
            ]
        );
    }

    private function autoGradeSubmission(GeneralExamSubmission $submission, GeneralExam $exam): void
    {
        $exam->load('questions');
        $responses      = $submission->responses ?? [];
        $totalScore     = 0;
        $totalMarks     = 0;
        $gradedResponses = [];

        foreach ($exam->questions as $question) {
            $questionId  = $question->id;
            $totalMarks += $question->marks;
            $response    = $responses[$questionId]['response'] ?? null;

            if ($response === null) {
                $gradedResponses[$questionId] = [
                    'response'    => null,
                    'is_correct'  => false,
                    'points_earned' => 0,
                    'answered_at' => null,
                ];
                continue;
            }

            if ($question->canAutoGrade()) {
                $gradeResult = $question->gradeResponse($response);
                $gradedResponses[$questionId] = array_merge($responses[$questionId], $gradeResult);
                $totalScore += $gradeResult['points_earned'];
            } else {
                $gradedResponses[$questionId] = array_merge($responses[$questionId], [
                    'is_correct'      => null,
                    'points_earned'   => 0,
                    'requires_grading' => true,
                ]);
            }
        }

        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        // ── Use the configurable grading system ──────────────────────────────
        $grade = $this->calculateGrade($percentage, $exam);

        $submission->update([
            'responses'          => $gradedResponses,
            'score'              => $totalScore,
            'total_marks'        => $totalMarks,
            'percentage'         => round($percentage, 2),
            'grade'              => $grade,
            'status'             => 'auto_graded',
        ]);
    }

    /**
     * Resolve a percentage to a grade label.
     *
     * Priority: exam owner's custom grade scales → built-in fallback.
     */
    private function calculateGrade(float $percentage, GeneralExam $exam): string
    {
        return $this->gradingService->resolveGrade(
            $percentage,
            $exam->user_id,
            $exam->school_id
        );
    }
}
