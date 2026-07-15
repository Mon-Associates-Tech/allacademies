<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Contracts\ExamSubmissionExportServiceInterface;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(private readonly ExamSubmissionExportServiceInterface $exportService) {}

    public function index(GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);

        $submissions = $exam->submissions()->latest('id')->paginate(20);

        return view('examination-hub.submissions.index', [
            'exam' => $exam,
            'submissions' => $submissions,
        ]);
    }

    public function show(GeneralExam $exam, GeneralExamSubmission $submission): View
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        return view('examination-hub.submissions.show', [
            'exam' => $exam,
            'submission' => $submission,
        ]);
    }

    public function grade(GeneralExam $exam, GeneralExamSubmission $submission): View
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        return view('examination-hub.submissions.grade', [
            'exam' => $exam,
            'submission' => $submission,
        ]);
    }

    public function export(GeneralExam $exam): StreamedResponse
    {
        $this->ensureOwnerAccess($exam);

        return $this->exportService->exportCsv($exam);
    }

    /** Export as Excel (.xlsx). Add the route: GET /exams/{exam}/submissions/export-excel */
    public function exportExcel(GeneralExam $exam): StreamedResponse
    {
        $this->ensureOwnerAccess($exam);

        return $this->exportService->exportExcel($exam);
    }
}