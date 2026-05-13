<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Contracts\ExamSubmissionExportServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamSubmissionExportService implements ExamSubmissionExportServiceInterface
{
    public function exportCsv(GeneralExam $exam): StreamedResponse
    {
        $exam->load(['submissions', 'questions.section', 'sections']);
        $questionCount = $exam->questions->count();
        $timeAllowed = (int) ($exam->duration_in_minutes ?? 0);
        $filename = 'examination-submissions-'.str($exam->title)->slug().'-'.$exam->id.'.csv';
        $questionSectionMap = $exam->questions->mapWithKeys(function ($question) {
            return [$question->id => $question->section?->title ?? 'Unsectioned'];
        });

        return response()->streamDownload(function () use ($exam, $questionCount, $timeAllowed, $questionSectionMap) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Participant Name',
                'Participant Email',
                'Score',
                'Total Marks',
                'Percentage',
                'Grade',
                'Question Count',
                'Correct Count',
                'Incorrect Count',
                'Unanswered Count',
                'Section Scores',
                'Time Allowed (minutes)',
                'Time Taken (minutes)',
                'Status',
                'Submitted At',
            ]);

            foreach ($exam->submissions as $submission) {
                $responses = is_array($submission->responses) ? $submission->responses : [];
                $correct = collect($responses)->where('is_correct', true)->count();
                $answered = collect($responses)->filter(fn ($r) => !empty($r['response']))->count();
                $incorrect = collect($responses)->where('is_correct', false)->filter(fn ($r) => !empty($r['response']))->count();
                $unanswered = $questionCount - $answered;
                
                $sectionScores = [];
                foreach ($responses as $questionId => $response) {
                    $sectionTitle = $questionSectionMap[(int) $questionId] ?? 'Unsectioned';
                    $points = (float) ($response['points_earned'] ?? 0);
                    if (! isset($sectionScores[$sectionTitle])) {
                        $sectionScores[$sectionTitle] = 0;
                    }
                    $sectionScores[$sectionTitle] += $points;
                }

                $sectionScoresFormatted = collect($sectionScores)
                    ->map(fn($score, $section) => "{$section}: {$score}")
                    ->join('; ');

                fputcsv($handle, [
                    $submission->participant_name ?? $submission->getParticipantName(),
                    $submission->participant_email ?? $submission->getParticipantEmail(),
                    $submission->score ?? 0,
                    $submission->total_marks ?? $exam->total_marks ?? 0,
                    round($submission->percentage ?? 0, 2).'%',
                    $submission->grade ?? 'N/A',
                    $questionCount,
                    $correct,
                    $incorrect,
                    $unanswered,
                    $sectionScoresFormatted,
                    $timeAllowed,
                    $submission->time_taken_minutes ?? 0,
                    ucfirst($submission->status ?? 'unknown'),
                    optional($submission->submitted_at)?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
