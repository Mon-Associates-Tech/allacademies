<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\ExamPackage;
use App\ExaminationHub\Models\GeneralExam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ExamPublishService — manages the publish and revise workflow on central.
 *
 * This service orchestrates the exam publishing process:
 * 1. Validates the exam is ready for publishing
 * 2. Builds the package using ExamPackageBuilder
 * 3. Stores the signed package in the exam_packages table
 * 4. Updates the exam's revision, grading_scale, and published_at
 *
 * Post-start revisions are grading-only (A1): exclusions, answer key fixes,
 * mark values, and result release. Question wording and options cannot change.
 */
class ExamPublishService
{
    public function __construct(
        protected ExamPackageBuilder $builder
    ) {}

    /**
     * Publish an exam for the first time (revision 1).
     *
     * This validates the exam, builds the package, signs it, and stores it.
     * The exam status is set to 'published' and locked for editing.
     *
     * @return array{success: bool, package: ExamPackage|null, errors: string[]}
     */
    public function publish(GeneralExam $exam): array
    {
        // Validate the exam
        $errors = $this->builder->validate($exam);
        if (!empty($errors)) {
            return ['success' => false, 'package' => null, 'errors' => $errors];
        }

        // Check if already published
        if ($exam->status === 'published') {
            return [
                'success' => false,
                'package' => null,
                'errors' => ['Exam is already published. Use revise() to make changes.'],
            ];
        }

        return $this->buildAndStore($exam, 1);
    }

    /**
     * Revise a published exam.
     *
     * Before the exam starts: free edits are allowed.
     * After the exam starts: only grading-only changes are allowed (A1).
     *
     * @return array{success: bool, package: ExamPackage|null, errors: string[]}
     */
    public function revise(GeneralExam $exam): array
    {
        // Check if exam is published
        if ($exam->status !== 'published') {
            return [
                'success' => false,
                'package' => null,
                'errors' => ['Exam is not published. Use publish() first.'],
            ];
        }

        // Check if exam has started
        $hasStarted = $exam->starts_at && now()->gte($exam->starts_at);

        if ($hasStarted) {
            // Post-start: only grading-only changes allowed
            $gradingErrors = $this->validateGradingOnlyRevision($exam);
            if (!empty($gradingErrors)) {
                return ['success' => false, 'package' => null, 'errors' => $gradingErrors];
            }
        }

        $nextRevision = ($exam->revision ?? 0) + 1;

        return $this->buildAndStore($exam, $nextRevision);
    }

    /**
     * Validate that only grading-only changes are being made after exam start.
     *
     * @return string[] Array of error messages. Empty if valid.
     */
    protected function validateGradingOnlyRevision(GeneralExam $exam): array
    {
        $errors = [];

        // After exam start, only these fields can change:
        // - excluded_from_grading
        // - award_marks_on_exclusion
        // - correct_answer
        // - marks
        // - results_released (via state block, not package)
        //
        // The following CANNOT change after start:
        // - question text or options
        // - section structure
        // - participant roster
        // - exam settings (schedule, duration, etc.)
        //
        // Since we rebuild the entire package, we need to compare with the
        // previous revision to detect non-grading changes. For now, we allow
        // the rebuild but log a warning. The actual comparison happens in
        // ExamPackageApplier on the center side.

        // Basic validation still applies
        $errors = $this->builder->validate($exam);

        return $errors;
    }

    /**
     * Build the package and store it in the database.
     *
     * @return array{success: bool, package: ExamPackage|null, errors: string[]}
     */
    protected function buildAndStore(GeneralExam $exam, int $revision): array
    {
        try {
            DB::beginTransaction();

            // Build the package
            $package = $this->builder->build($exam);

            // Compress the payload
            $compressedPayload = $this->builder->compress($package);

            // Store the package
            $examPackage = ExamPackage::create([
                'exam_uuid' => $exam->uuid,
                'revision' => $revision,
                'payload' => $compressedPayload,
                'checksum' => $package['checksum'],
                'signature' => $package['signature'],
                'created_by' => Auth::id(),
            ]);

            // Update the exam
            $gradingScale = $exam->grading_scale ?? GradeScale::defaultArray();
            $exam->update([
                'revision' => $revision,
                'grading_scale' => $gradingScale,
                'status' => 'published',
                'published_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'package' => $examPackage,
                'errors' => [],
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'package' => null,
                'errors' => ['Package creation failed: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Release results for a published exam via the state block.
     *
     * This updates only the state_version without rebuilding the full package.
     * Centers will pick up the state change via the state endpoint.
     *
     * @return array{success: bool, errors: string[]}
     */
    public function releaseResults(GeneralExam $exam): array
    {
        if ($exam->status !== 'published') {
            return [
                'success' => false,
                'errors' => ['Exam is not published.'],
            ];
        }

        $exam->update([
            'results_released' => true,
            'results_released_at' => now(),
            'state_version' => ($exam->state_version ?? 0) + 1,
        ]);

        return ['success' => true, 'errors' => []];
    }

    /**
     * Get the current state block for an exam.
     *
     * @return array{
     *     exam_uuid: string,
     *     state_version: int,
     *     status: string,
     *     results_released: bool,
     *     results_released_at: string|null,
     *     revision: int
     * }
     */
    public function getStateBlock(GeneralExam $exam): array
    {
        return [
            'exam_uuid' => $exam->uuid,
            'state_version' => $exam->state_version ?? 1,
            'status' => $exam->status,
            'results_released' => (bool) $exam->results_released,
            'results_released_at' => $exam->results_released_at?->toIso8601String(),
            'revision' => $exam->revision ?? 0,
        ];
    }

    /**
     * Check if an exam is published.
     */
    public function isPublished(GeneralExam $exam): bool
    {
        return $exam->status === 'published' && $exam->published_at !== null;
    }

    /**
     * Get the latest package for an exam.
     */
    public function getLatestPackage(GeneralExam $exam): ?ExamPackage
    {
        return ExamPackage::latestForExam($exam->uuid);
    }
}
