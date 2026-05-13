<?php

namespace App\ExaminationHub\Contracts;

use App\ExaminationHub\Models\GeneralExam;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ExamSubmissionExportServiceInterface
{
    public function exportCsv(GeneralExam $exam): StreamedResponse;
}

