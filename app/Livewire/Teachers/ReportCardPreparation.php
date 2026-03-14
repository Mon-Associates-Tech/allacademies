<?php

namespace App\Livewire\Teachers;

use App\Models\ReportCard;
use App\Models\ReportCardConfiguration;
use App\Models\ReportCardGrade;
use App\Models\Student;
use App\Services\ReportCardService;
use Livewire\Component;

class ReportCardPreparation extends Component
{
    public $selectedConfigId;
    public $selectedStudentId;
    public $reportCard;
    public $grades = [];
    public $mode = 'hybrid';

    protected $queryString = ['selectedConfigId', 'selectedStudentId'];

    public function mount()
    {
        if ($this->selectedConfigId && $this->selectedStudentId) {
            $this->loadReportCard();
        }
    }

    public function loadReportCard()
    {
        $student = Student::findOrFail($this->selectedStudentId);
        
        $this->reportCard = ReportCard::with(['grades.subject', 'configuration'])
            ->where('student_id', $student->id)
            ->where('report_card_configuration_id', $this->selectedConfigId)
            ->first();

        if (!$this->reportCard) {
            $service = app(ReportCardService::class);
            $this->reportCard = $service->generateReportCard($student, $this->selectedConfigId, $this->mode);
        }

        $this->loadGrades();
    }

    public function loadGrades()
    {
        foreach ($this->reportCard->grades as $grade) {
            $this->grades[$grade->id] = [
                'subject_id' => $grade->subject_id,
                'subject_name' => $grade->subject->name,
                'scores' => $grade->scores ?? [],
                'total_score' => $grade->total_score,
                'grade_label' => $grade->grade_label,
                'remarks' => $grade->remarks,
                'can_edit' => $grade->canBeEditedBy(auth()->user()),
            ];
        }
    }

    public function autoCalculate($gradeId)
    {
        $grade = ReportCardGrade::findOrFail($gradeId);
        $student = $this->reportCard->student;
        $config = $this->reportCard->configuration;

        $service = app(ReportCardService::class);
        $scores = $service->calculateScoresFromAssignments(
            $student,
            $grade->subject_id,
            $config->academicPeriod
        );

        $this->grades[$gradeId]['scores'] = $scores;
        $this->grades[$gradeId]['total_score'] = array_sum($scores);
        
        $this->saveGrade($gradeId);
    }

    public function saveGrade($gradeId)
    {
        $grade = ReportCardGrade::findOrFail($gradeId);
        
        if (!$grade->canBeEditedBy(auth()->user())) {
            session()->flash('error', 'You do not have permission to edit this subject');
            return;
        }

        $gradeData = $this->grades[$gradeId];
        
        $grade->update([
            'scores' => $gradeData['scores'],
            'total_score' => $gradeData['total_score'],
            'remarks' => $gradeData['remarks'],
            'last_modified_by' => auth()->id(),
            'last_modified_at' => now(),
        ]);

        $grade->assignGrade();
        $grade->save();

        $this->grades[$gradeId]['grade_label'] = $grade->grade_label;
        
        session()->flash('success', 'Grade saved successfully');
    }

    public function saveAll()
    {
        foreach ($this->grades as $gradeId => $gradeData) {
            if ($gradeData['can_edit']) {
                $this->saveGrade($gradeId);
            }
        }

        session()->flash('success', 'All grades saved successfully');
    }

    public function submitForApproval()
    {
        if ($this->reportCard->configuration->requires_approval) {
            $this->reportCard->submit();
            session()->flash('success', 'Report card submitted for approval');
        } else {
            $this->reportCard->update(['status' => 'published', 'is_accessible' => true]);
            session()->flash('success', 'Report card published');
        }

        return redirect()->route('teachers.report-cards');
    }

    public function render()
    {
        $teacher = auth()->user()->teacher;
        
        $configurations = ReportCardConfiguration::with(['academicPeriod', 'academicLevel'])
            ->where('school_id', getSchoolId())
            ->whereHas('academicLevel.teachers', fn($q) => $q->where('teachers.id', $teacher->id))
            ->latest()
            ->get();

        $students = [];
        if ($this->selectedConfigId) {
            $config = ReportCardConfiguration::findOrFail($this->selectedConfigId);
            $students = Student::where('academic_level_id', $config->academic_level_id)
                ->where('status', 'active')
                ->with('user')
                ->get();
        }

        return view('livewire.teachers.report-card-preparation', compact('configurations', 'students'));
    }
}
