<?php

namespace App\Examinations\Services;

use App\Examinations\Contracts\ExamSubmissionExportServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamSubmissionExportService implements ExamSubmissionExportServiceInterface
{
    // ─── Shared column definitions ────────────────────────────────────────────

    private function columns(): array
    {
        return [
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
        ];
    }

    private function buildRow(
        $submission,
        int $questionCount,
        int $timeAllowed,
        $questionSectionMap
    ): array {
        $responses   = is_array($submission->responses) ? $submission->responses : [];
        $correct     = collect($responses)->where('is_correct', true)->count();
        $answered    = collect($responses)->filter(fn ($r) => !empty($r['response']))->count();
        $incorrect   = collect($responses)->where('is_correct', false)->filter(fn ($r) => !empty($r['response']))->count();
        $unanswered  = $questionCount - $answered;

        $sectionScores = [];
        foreach ($responses as $questionId => $response) {
            $sectionTitle = $questionSectionMap[(int) $questionId] ?? 'Unsectioned';
            $sectionScores[$sectionTitle] = ($sectionScores[$sectionTitle] ?? 0) + (float) ($response['points_earned'] ?? 0);
        }

        $sectionScoresFormatted = collect($sectionScores)
            ->map(fn ($score, $section) => "{$section}: {$score}")
            ->join('; ');

        return [
            $submission->participant_name  ?? $submission->getParticipantName(),
            $submission->participant_email ?? $submission->getParticipantEmail(),
            $submission->score             ?? 0,
            $submission->total_marks       ?? 0,
            round($submission->percentage  ?? 0, 2) . '%',
            $submission->grade             ?? 'N/A',
            $questionCount,
            $correct,
            $incorrect,
            $unanswered,
            $sectionScoresFormatted,
            $timeAllowed,
            $submission->time_taken_minutes ?? 0,
            ucfirst($submission->status    ?? 'unknown'),
            optional($submission->submitted_at)?->format('Y-m-d H:i:s'),
        ];
    }

    // ─── CSV export ───────────────────────────────────────────────────────────

    public function exportCsv(GeneralExam $exam): StreamedResponse
    {
        $exam->load(['submissions', 'questions.section', 'sections']);

        $questionCount    = $exam->questions->count();
        $timeAllowed      = (int) ($exam->duration_in_minutes ?? 0);
        $questionSectionMap = $exam->questions->mapWithKeys(
            fn ($q) => [$q->id => $q->section?->title ?? 'Unsectioned']
        );

        $filename = 'submissions-' . str($exam->title)->slug() . '-' . $exam->id . '.csv';

        return response()->streamDownload(function () use ($exam, $questionCount, $timeAllowed, $questionSectionMap) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $this->columns());

            foreach ($exam->submissions as $submission) {
                fputcsv($handle, $this->buildRow($submission, $questionCount, $timeAllowed, $questionSectionMap));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ─── Excel export ─────────────────────────────────────────────────────────

    public function exportExcel(GeneralExam $exam): StreamedResponse
    {
        $exam->load(['submissions', 'questions.section', 'sections']);

        $questionCount      = $exam->questions->count();
        $timeAllowed        = (int) ($exam->duration_in_minutes ?? 0);
        $questionSectionMap = $exam->questions->mapWithKeys(
            fn ($q) => [$q->id => $q->section?->title ?? 'Unsectioned']
        );

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Submissions');

        // ── Header row ───────────────────────────────────────────────────────
        $columns = $this->columns();
        foreach ($columns as $col => $heading) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, 1);
            $cell->setValue($heading);
        }

        $headerRange = 'A1:' . $sheet->getHighestColumn() . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Data rows ────────────────────────────────────────────────────────
        $rowIndex = 2;
        foreach ($exam->submissions as $submission) {
            $data = $this->buildRow($submission, $questionCount, $timeAllowed, $questionSectionMap);
            foreach ($data as $col => $value) {
                $sheet->setCellValueByColumnAndRow($col + 1, $rowIndex, $value);
            }

            // Colour-code grade cell (column F = index 5)
            $grade     = $submission->grade ?? 'N/A';
            $gradeCell = $sheet->getCellByColumnAndRow(6, $rowIndex)->getCoordinate();
            $sheet->getStyle($gradeCell)->getFont()->setBold(true);
            $sheet->getStyle($gradeCell)->getFont()->setColor(
                (new \PhpOffice\PhpSpreadsheet\Style\Color($this->gradeColor($grade)))
            );

            $rowIndex++;
        }

        // ── Summary sheet ────────────────────────────────────────────────────
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $this->buildSummarySheet($summarySheet, $exam);

        // ── Auto-size columns ────────────────────────────────────────────────
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'submissions-' . str($exam->title)->slug() . '-' . $exam->id . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ─── Summary sheet builder ────────────────────────────────────────────────

    private function buildSummarySheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, GeneralExam $exam): void
    {
        $submissions = $exam->submissions;

        $totalSubmissions = $submissions->count();
        $avgScore         = $totalSubmissions > 0
            ? round($submissions->avg('percentage'), 2)
            : 0;
        $passCount        = $submissions->filter(fn ($s) => ($s->percentage ?? 0) >= 50)->count();
        $passRate         = $totalSubmissions > 0 ? round(($passCount / $totalSubmissions) * 100, 2) : 0;

        $rows = [
            ['Metric',              'Value'],
            ['Exam Title',          $exam->title],
            ['Total Submissions',   $totalSubmissions],
            ['Average Score (%)',   $avgScore . '%'],
            ['Pass Rate',           $passRate . '%'],
            ['Highest Score (%)',   $submissions->max('percentage') . '%'],
            ['Lowest Score (%)',    $submissions->min('percentage') . '%'],
            ['Export Generated At', now()->format('Y-m-d H:i:s')],
        ];

        foreach ($rows as $rowIndex => $row) {
            $sheet->fromArray($row, null, 'A' . ($rowIndex + 1));
        }

        // Bold header
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        // Grade distribution
        $startRow = count($rows) + 3;
        $sheet->setCellValue("A{$startRow}", 'Grade Distribution');
        $sheet->getStyle("A{$startRow}")->getFont()->setBold(true);
        $startRow++;
        $sheet->fromArray(['Grade', 'Count', 'Percentage'], null, "A{$startRow}");
        $sheet->getStyle("A{$startRow}:C{$startRow}")->getFont()->setBold(true);

        $gradeGroups = $submissions->groupBy('grade');
        foreach ($gradeGroups as $grade => $group) {
            $startRow++;
            $pct = $totalSubmissions > 0 ? round(($group->count() / $totalSubmissions) * 100, 1) : 0;
            $sheet->fromArray([$grade, $group->count(), $pct . '%'], null, "A{$startRow}");
        }

        foreach (['A', 'B'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function gradeColor(string $grade): string
    {
        return match (true) {
            in_array($grade, ['A+', 'A'], true)  => 'FF16A34A',
            in_array($grade, ['B+', 'B'], true)  => 'FF65A30D',
            in_array($grade, ['C+', 'C'], true)  => 'FFFACC15',
            $grade === 'D'                        => 'FFFB923C',
            default                               => 'FFEF4444',
        };
    }
}
