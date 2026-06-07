<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubmission;

class MockExamSectionLockingService
{
    public function lockExpiredSections(MockExam $mockExam): int
    {
        $submissions = $mockExam->submissions()
            ->where('status', MockExamSubmission::STATUS_IN_PROGRESS)
            ->get();

        $lockedCount = 0;

        foreach ($submissions as $submission) {
            $timings = $submission->section_timings ?? [];

            foreach ($timings as $sectionId => $timing) {
                if ($timing['submitted_at'] === null && $submission->isSectionExpired((int) $sectionId)) {
                    $timings[$sectionId]['submitted_at'] = now()->toIso8601String();
                    $lockedCount++;
                }
            }

            if ($lockedCount > 0) {
                $submission->update(['section_timings' => $timings]);
            }
        }

        return $lockedCount;
    }

    public function releaseSection(MockExamSubmission $submission, int $sectionIndex): void
    {
        $submission->update(['current_section_index' => $sectionIndex]);
    }

    public function canAccessSection(MockExamSubmission $submission, int $sectionId): bool
    {
        $timings = $submission->section_timings ?? [];

        if (! isset($timings[$sectionId])) {
            return true;
        }

        if ($timings[$sectionId]['submitted_at'] !== null) {
            return false;
        }

        return ! $submission->isSectionExpired($sectionId);
    }
}
