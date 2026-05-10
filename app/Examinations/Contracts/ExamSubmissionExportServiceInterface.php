<?php

namespace App\Examinations\Contracts;

use App\Models\GeneralExam;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ExamSubmissionExportServiceInterface
{
    public function exportCsv(GeneralExam $exam): StreamedResponse;
}

