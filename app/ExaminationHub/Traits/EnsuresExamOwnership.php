<?php

namespace App\ExaminationHub\Traits;

use App\ExaminationHub\Models\GeneralExam;

trait EnsuresExamOwnership
{
    protected function ensureOwnerAccess(GeneralExam $exam): void
    {
        $teacherId = optional(auth()->user()?->teacher)->id;

        abort_unless(
            $exam->user_id === auth()->id() || ($teacherId && $exam->teacher_id === $teacherId),
            403
        );
    }
}

