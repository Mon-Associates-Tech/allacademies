<?php

namespace App\Examinations\Contracts;

use App\Models\GeneralExam;
use App\Models\GeneralExamParticipant;

interface ExamParticipantAccessServiceInterface
{
    public function registerConfiguredParticipant(GeneralExam $exam, array $payload): array;

    public function importConfiguredParticipants(GeneralExam $exam, string $csvPath): array;

    public function authorizeJoinByCode(GeneralExam $exam, string $name, string $email, ?string $uniqueCode = null): array;

    public function createOrReuseParticipant(string $name, string $email): GeneralExamParticipant;
}

