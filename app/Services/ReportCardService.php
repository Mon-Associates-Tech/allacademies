<?php

namespace App\Services;

use App\Models\AcademicPeriod;
use App\Models\ReportCard;
use App\Models\ReportCardGrade;
use App\Models\ScoreWeighting;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

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
            // Map weighting name to assignment type
            // Default mapping: 'Quiz' -> 'quiz', 'Examination' -> 'examination'
            // If weighting name doesn't match, we might need a more flexible way to map
            $assignmentType = strtolower($weighting->name);

            $relevantSubmissions = $submissions->filter(function ($submission) use ($assignmentType) {
                return strtolower($submission->assignment->type) === $assignmentType;
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
                $scores[$weighting->name] = ($percentage / 100) * $weighting->weight_percentage;
            } else {
                $scores[$weighting->name] = 0;
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

            $reportCard = ReportCard::create([
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'academic_year_id' => $config->academicPeriod->academic_year_id ?? null,
                'term' => $config->academicPeriod->name,
                'report_card_configuration_id' => $configurationId,
                'status' => 'draft',
                'generated_at' => now(),
            ]);

            $subjects = $student->getAllAccessibleSubjects();

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
                $this->generateReportCard($student, $configurationId, $mode);
                $count++;
            }
        }

        return $count;
    }
}
