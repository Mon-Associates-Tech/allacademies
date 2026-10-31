<?php

namespace App\ExaminationHub\Traits;

use App\ExaminationHub\Models\GeneralExam;

trait EnsuresExamOwnership
{
    protected function ensureOwnerAccess(GeneralExam $exam): void
    {
        // On center instances, staff can monitor but not edit
        $role = config('center.role', 'central');
        if ($role === 'center') {
            // Center staff can view but cannot edit content
            abort(403, 'Content editing is not allowed on center instances.');
        }

        $teacherId = optional(auth()->user()?->teacher)->id;

        abort_unless(
            $exam->user_id === auth()->id() || ($teacherId && $exam->teacher_id === $teacherId),
            403
        );
    }

    /**
     * Check if the current user can monitor an exam.
     * Center staff can monitor; central users use ownership check.
     */
    protected function ensureMonitorAccess(GeneralExam $exam): void
    {
        $role = config('center.role', 'central');

        // On center instances, any authenticated user can monitor
        if ($role === 'center') {
            return;
        }

        // On central, use ownership check
        $this->ensureOwnerAccess($exam);
    }
}

