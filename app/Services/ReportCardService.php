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
            ->whereBetween('submitted_at', [$period->start_date, $period->end_date])
            ->get();

        $scores = [];
        foreach ($weightings as $weighting) {
            $relevantSubmissions = $submissions; // Can filter by assignment type if needed
            
            $totalObtained = $relevantSubmissions->sum('score');
            $totalPossible = $relevantSubmissions->sum('total_marks');
            
            if ($totalPossible > 0) {
                $percentage = ($totalObtained / $totalPossible) * 100;
                $scores[$weighting->name] = ($percentage / 100) * $weighting->weight_percentage;
            } else {
                $scores[$weighting->name] = 0;
            }
        }

        return $scores;
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
                
                if ($mode === 'automated' || $mode === 'hybrid') {
                    $scores = $this->calculateScoresFromAssignments(
                        $student,
                        $subject->id,
                        $config->academicPeriod
                    );
                }

                $grade = ReportCardGrade::create([
                    'report_card_id' => $reportCard->id,
                    'subject_id' => $subject->id,
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

        $count = 0;
        foreach ($students as $student) {
            // Check if already exists
            $exists = ReportCard::where('student_id', $student->id)
                ->where('report_card_configuration_id', $configurationId)
                ->exists();

            if (!$exists) {
                $this->generateReportCard($student, $configurationId, $config->preparation_mode);
                $count++;
            }
        }

        return $count;
    }
}
