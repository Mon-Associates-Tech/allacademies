<?php

namespace App\ExaminationHub\Contracts;

use App\ExaminationHub\Models\GeneralExam;

interface ExamCreationServiceInterface
{
    public function createExam(int $userId, array $payload): GeneralExam;

    public function updateExam(GeneralExam $exam, int $userId, array $payload): GeneralExam;
}
