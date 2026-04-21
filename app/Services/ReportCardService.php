<?php

namespace App\Services;

use App\Models\AcademicPeriod;
use App\Models\ReportCard;
use App\Models\ReportCardGrade;
use App\Models\ScoreWeighting;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
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

            if ($subjects->isEmpty()) {
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

        $students = Student::where('academic_level_id', $config->academic_level_id)
            ->where('status', 'active')
            ->get();

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

        if (! $exists) {
            $this->generateReportCard($student, $configurationId, $config->preparation_mode);

            return 1;
        }

        return 0;
    }

    protected function generateForStudents($students, $configurationId, $mode): int
    {
        $count = 0;
        foreach ($students as $student) {
            // Check if already exists
            $exists = ReportCard::where('student_id', $student->id)
                ->where('report_card_configuration_id', $configurationId)
                ->exists();

            if (! $exists) {
                try {
                    $this->generateReportCard($student, $configurationId, $mode);
                    $count++;
                } catch (\RuntimeException $exception) {
                    // Skip students without eligible/assigned subjects or outside configured limits.
                    continue;
                }
            }
        }

        return $count;
    }

    public function getAssignedSubjectsForStudent(Student $student)
    {
        return $student->individualSubjects()
            ->wherePivot('is_active', true)
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
