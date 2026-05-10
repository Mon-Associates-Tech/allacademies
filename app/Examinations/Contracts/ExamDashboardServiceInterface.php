<?php

namespace App\Examinations\Contracts;

use App\Models\GeneralExam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ExamDashboardServiceInterface
{
    public function listForOwner(int $userId, array $filters = []): LengthAwarePaginator;

    public function summaryForOwner(int $userId): array;

    public function sectionNavigator(GeneralExam $exam): array;
}

