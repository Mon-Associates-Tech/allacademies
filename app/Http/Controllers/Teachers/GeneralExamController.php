<?php

namespace App\Http\Controllers\Teachers;

use App\Http\Controllers\Controller;
use App\Models\GeneralExam;
use App\Models\GeneralExamSubmission;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class GeneralExamController extends Controller
{
    /**
     * Display a listing of public assignments.
     */
    public function index(): View
    {
        return view('teachers.general-exams.index');
    }

    /**
     * Show the form for creating a new public assignment.
     */
    public function create(): View
    {
        return view('teachers.general-exams.create');
    }

    /**
     * Display the specified public assignment.
     */
    public function show(GeneralExam $assignment): View
    {
        return view('teachers.general-exams.show', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show the form for editing the specified public assignment.
     */
    public function edit(GeneralExam $assignment): View
    {
        return view('teachers.general-exams.edit', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Display the results for a public assignment.
     */
    public function results(GeneralExam $assignment): View
    {
        return view('teachers.general-exams.results', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show the grading form for a submission.
     */
    public function gradeSubmission(GeneralExamSubmission $submission): View
    {
        return view('teachers.general-exams.grade-submission', [
            'submission' => $submission,
        ]);
    }

    /**
     * Export submission results as CSV.
     */
    public function export(GeneralExam $assignment): StreamedResponse
    {
        abort_unless(
            $assignment->user_id === auth()->id() || $assignment->teacher_id === optional(auth()->user()->teacher)->id,
            403
        );

        $assignment->load(['submissions.participant', 'questions']);
        $questionCount = $assignment->questions->count();
        $timeAllowedSeconds = (int) ($assignment->duration_in_minutes ?? 0) * 60;

        $filename = 'exam-results-'.str($assignment->title)->slug().'-'.$assignment->id.'.csv';

        return response()->streamDownload(function () use ($assignment, $questionCount, $timeAllowedSeconds) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'participant_name',
                'participant_email',
                'participant_type',
                'status',
                'grade',
                'score',
                'total_marks',
                'percentage',
                'question_count',
                'correct_count',
                'incorrect_count',
                'time_allowed_seconds',
                'time_taken_seconds',
                'submitted_at',
            ]);

            foreach ($assignment->submissions as $submission) {
                $gradedResponses = is_array($submission->responses) ? $submission->responses : [];
                $correctCount = collect($gradedResponses)->filter(fn ($response) => ($response['is_correct'] ?? false) === true)->count();
                $answeredCount = collect($gradedResponses)->filter(fn ($response) => array_key_exists('response', $response) && $response['response'] !== null && $response['response'] !== '')->count();
                $incorrectCount = max(0, $answeredCount - $correctCount);

                fputcsv($handle, [
                    $submission->getParticipantName(),
                    $submission->getParticipantEmail(),
                    class_basename((string) $submission->participant_type),
                    $submission->status,
                    $submission->grade,
                    $submission->score,
                    $submission->total_marks,
                    $submission->percentage,
                    $questionCount,
                    $correctCount,
                    $incorrectCount,
                    $timeAllowedSeconds,
                    $submission->time_spent_seconds,
                    optional($submission->submitted_at)?->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
