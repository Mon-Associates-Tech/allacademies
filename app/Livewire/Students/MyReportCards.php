<?php

namespace App\Livewire\Students;

use App\Models\ReportCard;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Livewire\Component;

class MyReportCards extends Component
{
    public $selectedReportCardId;

    public function downloadReportCard($reportCardId)
    {
        $reportCard = ReportCard::with([
            'student.user',
            'student.academicLevel',
            'student.school',
            'grades.subject',
            'configuration.academicPeriod',
        ])->findOrFail($reportCardId);

        // Check access
        if ($reportCard->student_id !== auth()->user()->student->id) {
            abort(403);
        }

        if (!$reportCard->is_accessible) {
            session()->flash('error', 'This report card is not yet available');
            return;
        }

        $pdf = PDF::loadView('reports.report-card-pdf', [
            'reportCard' => $reportCard,
            'school' => $reportCard->student->school,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "report-card-{$reportCard->student->user->name}-{$reportCard->term}.pdf");
    }

    public function render()
    {
        $student = auth()->user()->student;

        $reportCards = ReportCard::with([
            'configuration.academicPeriod',
            'configuration.academicLevel',
            'grades',
        ])
            ->where('student_id', $student->id)
            ->where('is_accessible', true)
            ->whereHas('configuration', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('is_published', true)
                        ->where(function ($q3) {
                            $q3->whereNull('available_from')
                                ->orWhere('available_from', '<=', now());
                        })
                        ->where(function ($q4) {
                            $q4->whereNull('available_until')
                                ->orWhere('available_until', '>=', now());
                        });
                });
            })
            ->whereDoesntHave('configuration.revocations', function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->where('revocation_type', 'student');
            })
            ->latest('generated_at')
            ->get();

        return view('livewire.students.my-report-cards', compact('reportCards'));
    }
}
