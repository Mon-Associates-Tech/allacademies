<?php

namespace App\ExaminationHub\Traits;

use App\ExaminationHub\Models\GeneralExam;

trait EnsuresExamOwnership
{
    protected function ensureOwnerAccess(GeneralExam $exam): void
    {
        $user = auth()->user();

        // Super admins and owners can always edit, even on center instances
        if ($user->isSuperAdmin() || $user->isOwner() || $user->isSchoolAdmin()) {
            return;
        }

        // On center instances, non-privileged staff can monitor but not edit
        if (config('center.role', 'central') === 'center') {
            //abort(403, 'Content editing is not allowed on center instances.');
        }

        $teacherId = optional($user->teacher)->id;

        abort_unless(
            $exam->user_id === $user->id || ($teacherId && $exam->teacher_id === $teacherId),
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

