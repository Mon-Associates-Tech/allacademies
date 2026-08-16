<?php

namespace App\Services;

use App\Models\AcademicPeriod;
use App\Models\ReportCard;
use App\Models\ReportCardGrade;
use App\Models\ScoreWeighting;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReportCardService
{
    public function calculateScoresFromAssignments(Student $student, $subjectId, AcademicPeriod $period): array
    {
        $weightings = ScoreWeighting::getForLevel($student->academic_level_id, $subjectId);

        $submissions = $student->assignmentSubmissions()
            ->where('status', 'graded')
            ->whereHas('assignment', function ($q) use ($subjectId) {
                $q->where('academic_subject_id', $subjectId);
            })
            ->whereBetween('submitted_at', [$period->start_date->startOfDay(), $period->end_date->endOfDay()])
            ->with(['assignment', 'assignment.teacher'])
            ->get();

        $scores = [];
        $teachers = [];

        foreach ($weightings as $weighting) {
            $sourceType = strtolower((string) ($weighting->source_type ?: $weighting->name));
            $scoreKey = $weighting->score_key ?: Str::slug($weighting->name, '_');

            if ($sourceType === 'manual') {
                $scores[$scoreKey] = 0;
                continue;
            }

            $relevantSubmissions = $submissions->filter(function ($submission) use ($sourceType) {
                if (in_array($sourceType, ['assignment', 'mixed'], true)) {
                    return true;
                }

                return strtolower((string) $submission->assignment->type) === $sourceType;
            });

            // Keep track of teachers who graded these submissions
            foreach ($relevantSubmissions as $submission) {
                if ($submission->assignment->teacher) {
                    $teachers[$submission->assignment->teacher->id] = ($teachers[$submission->assignment->teacher->id] ?? 0) + 1;
                }
            }

            $totalObtained = $relevantSubmissions->sum('score');
            $totalPossible = $relevantSubmissions->sum('total_marks');

            if ($totalPossible > 0) {
                $percentage = ($totalObtained / $totalPossible) * 100;
                $scores[$scoreKey] = ($percentage / 100) * $weighting->weight_percentage;
            } else {
                $scores[$scoreKey] = 0;
            }
        }

        // Determine the most frequent teacher for this subject in this period
        $mostFrequentTeacherId = null;
        if (! empty($teachers)) {
            arsort($teachers);
            $mostFrequentTeacherId = key($teachers);
        }

        return [
            'scores' => $scores,
            'teacher_id' => $mostFrequentTeacherId,
        ];
    }

    public function generateReportCard(Student $student, $configurationId, $mode = 'hybrid'): ReportCard
    {
        return DB::transaction(function () use ($student, $configurationId, $mode) {
            $config = \App\Models\ReportCardConfiguration::findOrFail($configurationId);
            $subjects = $this->getAssignedSubjectsForStudent($student);

            Log::info('ReportCardService: assigned subjects found', [
                'student_id' => $student->id,
                'configuration_id' => $configurationId,
                'subject_count' => $subjects->count(),
                'subject_ids' => $subjects->pluck('id')->toArray(),
                'min_subjects' => $config->min_subjects,
                'max_subjects' => $config->max_subjects,
            ]);

            if ($subjects->isEmpty()) {
                Log::warning('ReportCardService: no subjects assigned for student', [
                    'student_id' => $student->id,
                    'configuration_id' => $configurationId,
                ]);

                throw new \RuntimeException("No subjects assigned for student {$student->id}. Assign subjects before generating report cards.");
            }

            $this->assertSubjectLimits($subjects->count(), $config->min_subjects, $config->max_subjects);

            $reportCard = ReportCard::create([
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'academic_year_id' => $config->academicPeriod->academic_year_id ?? null,
                'term' => $config->academicPeriod->name,
                'report_card_configuration_id' => $configurationId,
                'status' => 'draft',
                'generated_at' => now(),
            ]);

            foreach ($subjects as $subject) {
                $scores = [];
                $calculatedTeacherId = null;

                if ($mode === 'automated' || $mode === 'hybrid') {
                    $result = $this->calculateScoresFromAssignments(
                        $student,
                        $subject->id,
                        $config->academicPeriod
                    );
                    $scores = $result['scores'];
                    $calculatedTeacherId = $result['teacher_id'];
                }

                $teacherId = $calculatedTeacherId
                    ?? $subject->teachers()
                        ->wherePivot('is_primary', true)
                        ->first()?->id
                    ?? $subject->teachers()->first()?->id
                    ?? $student->primary_teacher?->id;

                // Last resort: find any teacher assigned to this subject (if not already found)
                // or any teacher in the school/level if absolutely necessary to avoid null
                if (! $teacherId) {
                    $teacherId = \App\Models\Teacher::where('school_id', $student->school_id)
                        ->whereHas('academicLevels', function ($q) use ($student) {
                            $q->where('academic_levels.id', $student->academic_level_id);
                        })
                        ->first()?->id;
                }

                // If STILL null (e.g. no teachers assigned to level), just get any teacher from school
                if (! $teacherId) {
                    $teacherId = \App\Models\Teacher::where('school_id', $student->school_id)->first()?->id;
                }

                $grade = ReportCardGrade::create([
                    'report_card_id' => $reportCard->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacherId,
                    'scores' => $scores,
                    'total_score' => array_sum($scores),
                ]);

                $grade->assignGrade();
                $grade->save();
            }

            return $reportCard->load('grades.subject');
        });
    }

    public function bulkGenerateForLevel($configurationId): int
    {
        $config = \App\Models\ReportCardConfiguration::with('academicLevel')->findOrFail($configurationId);

        $students = Student::where('school_id', $config->school_id)
            ->where('academic_level_id', $config->academic_level_id)
            ->where('status', 'active')
            ->get();

        Log::info('ReportCardService: bulkGenerateForLevel started', [
            'configuration_id' => $configurationId,
            'school_id' => $config->school_id,
            'academic_level_id' => $config->academic_level_id,
            'students_found' => $students->count(),
            'student_ids' => $students->pluck('id')->toArray(),
        ]);

        if ($students->isEmpty()) {
            Log::warning('ReportCardService: no active students found for level', [
                'configuration_id' => $configurationId,
                'school_id' => $config->school_id,
                'academic_level_id' => $config->academic_level_id,
            ]);

            return 0;
        }

        return $this->generateForStudents($students, $configurationId, $config->preparation_mode);
    }
    public function generateForGroup($groupId, $configurationId): int
    {
        $config = \App\Models\ReportCardConfiguration::findOrFail($configurationId);

        $students = Student::where('academic_group_id', $groupId)
            ->where('academic_level_id', $config->academic_level_id)
            ->where('status', 'active')
            ->get();

        return $this->generateForStudents($students, $configurationId, $config->preparation_mode);
    }

    public function generateForStudent(Student $student, $configurationId): int
    {
        $config = \App\Models\ReportCardConfiguration::findOrFail($configurationId);

        $exists = ReportCard::where('student_id', $student->id)
            ->where('report_card_configuration_id', $configurationId)
            ->exists();

        if ($exists) {
            Log::info('ReportCardService: report card already exists, skipping student', [
                'student_id' => $student->id,
                'configuration_id' => $configurationId,
            ]);

            return 0;
        }

        try {
            $this->generateReportCard($student, $configurationId, $config->preparation_mode);

            Log::info('ReportCardService: report card generated for student', [
                'student_id' => $student->id,
                'configuration_id' => $configurationId,
            ]);

            return 1;
        } catch (\Throwable $e) {
            Log::error('ReportCardService: generateForStudent failed', [
                'student_id' => $student->id,
                'configuration_id' => $configurationId,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            throw $e;
        }
    }
    protected function generateForStudents($students, $configurationId, $mode): int
    {
        $count = 0;

        foreach ($students as $student) {
            $exists = ReportCard::where('student_id', $student->id)
                ->where('report_card_configuration_id', $configurationId)
                ->exists();

            if ($exists) {
                Log::info('ReportCardService: skipping student because report card already exists', [
                    'student_id' => $student->id,
                    'configuration_id' => $configurationId,
                ]);

                continue;
            }

            try {
                Log::info('ReportCardService: attempting to generate report card', [
                    'student_id' => $student->id,
                    'configuration_id' => $configurationId,
                    'mode' => $mode,
                ]);

                $this->generateReportCard($student, $configurationId, $mode);

                $count++;

                Log::info('ReportCardService: successfully generated report card', [
                    'student_id' => $student->id,
                    'configuration_id' => $configurationId,
                ]);
            } catch (\Throwable $e) {
                Log::error('ReportCardService: skipping student due to generation failure', [
                    'student_id' => $student->id,
                    'configuration_id' => $configurationId,
                    'mode' => $mode,
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                ]);

                continue;
            }
        }

        Log::info('ReportCardService: bulk generation completed', [
            'configuration_id' => $configurationId,
            'generated_count' => $count,
        ]);

        return $count;
    }
    public function getAssignedSubjectsForStudent(Student $student)
    {
        // 1. Try individual subjects first (your current logic)
        $individualSubjects = $student->individualSubjects()
            ->wherePivot('is_active', true)
            ->get();

        if ($individualSubjects->isNotEmpty()) {
            return $individualSubjects->sortBy('name')->values();
        }

        // 2. Fallback: Check if subjects are assigned to the student's Group/Class
        if ($student->academic_group_id && method_exists($student->academicGroup, 'subjects')) {
            $groupSubjects = $student->academicGroup->subjects;
            if ($groupSubjects && $groupSubjects->isNotEmpty()) {
                return $groupSubjects->sortBy('name')->values();
            }
        }

        // 3. Fallback: Check if subjects are assigned to the student's Academic Level
        if ($student->academic_level_id && method_exists($student->academicLevel, 'subjects')) {
            $levelSubjects = $student->academicLevel->subjects;
            if ($levelSubjects && $levelSubjects->isNotEmpty()) {
                return $levelSubjects->sortBy('name')->values();
            }
        }

        // 4. Last Resort: If no assignments exist anywhere, grab all active subjects for the school
        return \App\Models\AcademicSubject::where('school_id', $student->school_id)
            ->orderBy('name')
            ->get();
    }
    private function assertSubjectLimits(int $count, ?int $min, ?int $max): void
    {
        if ($min !== null && $count < $min) {
            throw new \RuntimeException("Assigned subjects ({$count}) is below required minimum ({$min}).");
        }

        if ($max !== null && $count > $max) {
            throw new \RuntimeException("Assigned subjects ({$count}) exceeds maximum allowed ({$max}).");
        }
    }
}
