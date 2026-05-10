<?php

namespace App\Examinations\Traits;

use App\Models\GeneralExam;

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

