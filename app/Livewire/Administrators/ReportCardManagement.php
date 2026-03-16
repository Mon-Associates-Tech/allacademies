<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\GradeScale;
use App\Models\ReportCard;
use App\Models\ReportCardConfiguration;
use App\Models\ScoreWeighting;
use App\Models\Student;
use App\Services\ReportCardService;
use Livewire\Component;
use Livewire\WithPagination;

class ReportCardManagement extends Component
{
    use WithPagination;

    public $activeTab = 'configurations';

    public $selectedYearId;

    public $selectedPeriodId;

    public $selectedLevelId;

    public $selectedGroupId;

    public $selectedStudentId;

    public $showConfigModal = false;

    public $showGradeScaleModal = false;

    public $showWeightingModal = false;

    // Configuration form
    public $configId;

    public $requiresApproval = true;

    public $isPublished = false;

    public $availableFrom;

    public $availableUntil;

    public $preparationMode = 'hybrid';

    // Grade Scale form
    public $gradeScaleId;

    public $gradeName;

    public $minScore;

    public $maxScore;

    public $letterGrade;

    public $gradePoint;

    public $gradeRemarks;

    public $isDefaultGrade = false;

    public $gradeLevelId;

    // Weighting form
    public $weightingId;

    public $weightingName;

    public $weightPercentage;

    public $weightingLevelId;

    public $weightingSubjectId;

    public $isDefaultWeighting = false;

    protected $queryString = ['activeTab', 'selectedYearId', 'selectedPeriodId', 'selectedLevelId', 'selectedGroupId', 'selectedStudentId'];

    public function mount()
    {
        $currentYear = AcademicYear::where('school_id', getSchoolId())
            ->where('is_current', true)
            ->first();

        $this->selectedYearId = $currentYear?->id;

        $this->selectedPeriodId = AcademicPeriod::where('school_id', getSchoolId())
            ->where('status', 'active')
            ->when($this->selectedYearId, fn ($q) => $q->where('academic_year_id', $this->selectedYearId))
            ->first()?->id;
    }

    public function openConfigModal($configId = null)
    {
        if ($configId) {
            $config = ReportCardConfiguration::findOrFail($configId);
            $this->configId = $config->id;
            $this->selectedPeriodId = $config->academic_period_id;
            $this->selectedLevelId = $config->academic_level_id;
            $this->requiresApproval = $config->requires_approval;
            $this->isPublished = $config->is_published;
            $this->availableFrom = $config->available_from?->format('Y-m-d\TH:i');
            $this->availableUntil = $config->available_until?->format('Y-m-d\TH:i');
            $this->preparationMode = $config->preparation_mode;
        } else {
            $this->reset(['configId', 'requiresApproval', 'isPublished', 'availableFrom', 'availableUntil', 'preparationMode']);
            $this->requiresApproval = true;
            $this->preparationMode = 'hybrid';
        }

        $this->dispatch('open-modal', name: 'configModal');
    }

    public function saveConfiguration()
    {
        $this->validate([
            'selectedPeriodId' => 'required|exists:academic_periods,id',
            'selectedLevelId' => 'required|exists:academic_levels,id',
            'preparationMode' => 'required|in:manual,automated,hybrid',
        ]);

        ReportCardConfiguration::updateOrCreate(
            ['id' => $this->configId],
            [
                'school_id' => getSchoolId(),
                'academic_period_id' => $this->selectedPeriodId,
                'academic_level_id' => $this->selectedLevelId,
                'requires_approval' => $this->requiresApproval,
                'is_published' => $this->isPublished,
                'available_from' => $this->availableFrom,
                'available_until' => $this->availableUntil,
                'preparation_mode' => $this->preparationMode,
            ]
        );

        $this->dispatch('close-modal', name: 'configModal');
        session()->flash('success', 'Configuration saved successfully');
    }

    public function generateReportCards($configId)
    {
        $service = app(ReportCardService::class);

        if ($this->selectedStudentId) {
            $student = Student::findOrFail($this->selectedStudentId);
            $count = $service->generateForStudent($student, $configId);
        } elseif ($this->selectedGroupId) {
            $count = $service->generateForGroup($this->selectedGroupId, $configId);
        } else {
            $count = $service->bulkGenerateForLevel($configId);
        }

        session()->flash('success', "Generated {$count} report cards");
    }

    public function approveReportCard($reportCardId)
    {
        $reportCard = ReportCard::findOrFail($reportCardId);
        $reportCard->approve(auth()->user());

        session()->flash('success', 'Report card approved');
    }

    public function rejectReportCard($reportCardId, $reason)
    {
        $reportCard = ReportCard::findOrFail($reportCardId);
        $reportCard->reject(auth()->user(), $reason);

        session()->flash('success', 'Report card rejected');
    }

    public function togglePublishConfig($configId)
    {
        $config = ReportCardConfiguration::findOrFail($configId);
        $config->is_published = ! $config->is_published;
        $config->save();

        session()->flash('success', $config->is_published ? 'Configuration published' : 'Configuration unpublished');
    }

    public function openGradeScaleModal($id = null)
    {
        if ($id) {
            $scale = GradeScale::findOrFail($id);
            $this->gradeScaleId = $scale->id;
            $this->gradeName = $scale->name;
            $this->minScore = $scale->min_score;
            $this->maxScore = $scale->max_score;
            $this->letterGrade = $scale->letter_grade;
            $this->gradePoint = $scale->grade_point;
            $this->gradeRemarks = $scale->remarks;
            $this->isDefaultGrade = $scale->is_default;
            $this->gradeLevelId = $scale->academic_level_id;
        } else {
            $this->reset(['gradeScaleId', 'gradeName', 'minScore', 'maxScore', 'letterGrade', 'gradePoint', 'gradeRemarks', 'isDefaultGrade', 'gradeLevelId']);
        }

        $this->dispatch('open-modal', name: 'gradeScaleModal');
    }

    public function saveGradeScale()
    {
        $this->validate([
            'gradeName' => 'required|string',
            'minScore' => 'required|numeric|min:0|max:100',
            'maxScore' => 'required|numeric|min:0|max:100|gte:minScore',
            'letterGrade' => 'required|string',
            'gradePoint' => 'nullable|numeric',
        ]);

        GradeScale::updateOrCreate(
            ['id' => $this->gradeScaleId],
            [
                'school_id' => getSchoolId(),
                'academic_level_id' => $this->gradeLevelId,
                'name' => $this->gradeName,
                'min_score' => $this->minScore,
                'max_score' => $this->maxScore,
                'letter_grade' => $this->letterGrade,
                'grade_point' => $this->gradePoint ?: null,
                'remarks' => $this->gradeRemarks,
                'is_default' => $this->isDefaultGrade,
            ]
        );

        $this->dispatch('close-modal', name: 'gradeScaleModal');
        session()->flash('success', 'Grade scale saved successfully');
    }

    public function openWeightingModal($id = null)
    {
        if ($id) {
            $weighting = ScoreWeighting::findOrFail($id);
            $this->weightingId = $weighting->id;
            $this->weightingName = $weighting->name;
            $this->weightPercentage = $weighting->weight_percentage;
            $this->weightingLevelId = $weighting->academic_level_id;
            $this->weightingSubjectId = $weighting->academic_subject_id;
            $this->isDefaultWeighting = $weighting->is_default;
        } else {
            $this->reset(['weightingId', 'weightingName', 'weightPercentage', 'weightingLevelId', 'weightingSubjectId', 'isDefaultWeighting']);
        }

        $this->dispatch('open-modal', name: 'weightingModal');
    }

    public function saveWeighting()
    {
        $this->validate([
            'weightingName' => 'required|string',
            'weightPercentage' => 'required|numeric|min:0|max:100',
        ]);

        ScoreWeighting::updateOrCreate(
            ['id' => $this->weightingId],
            [
                'school_id' => getSchoolId(),
                'academic_level_id' => $this->weightingLevelId,
                'academic_subject_id' => $this->weightingSubjectId,
                'name' => $this->weightingName,
                'weight_percentage' => $this->weightPercentage,
                'is_default' => $this->isDefaultWeighting,
            ]
        );

        $this->dispatch('close-modal', name: 'weightingModal');
        session()->flash('success', 'Score weighting saved successfully');
    }

    public function render()
    {
        $years = AcademicYear::where('school_id', getSchoolId())->latest()->get();

        $periods = AcademicPeriod::where('school_id', getSchoolId())
            ->when($this->selectedYearId, fn ($q) => $q->where('academic_year_id', $this->selectedYearId))
            ->latest()
            ->get();

        $levels = AcademicLevel::whereHas('schools', function ($q) {
            $q->where('school_id', getSchoolId());
        })->get();

        $groups = AcademicGroup::forSchool(getSchoolId())->get();

        $students = [];
        if ($this->selectedLevelId) {
            $students = Student::where('academic_level_id', $this->selectedLevelId)
                ->when($this->selectedGroupId, fn ($q) => $q->where('academic_group_id', $this->selectedGroupId))
                ->where('status', 'active')
                ->with('user')
                ->get();
        }

        $configurations = ReportCardConfiguration::with(['academicPeriod', 'academicLevel'])
            ->where('school_id', getSchoolId())
            ->when($this->selectedPeriodId, fn ($q) => $q->where('academic_period_id', $this->selectedPeriodId))
            ->when($this->selectedLevelId, fn ($q) => $q->where('academic_level_id', $this->selectedLevelId))
            ->latest()
            ->get();

        $reportCards = ReportCard::with(['student.user', 'configuration.academicLevel'])
            ->where('school_id', getSchoolId())
            ->when($this->selectedLevelId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('academic_level_id', $this->selectedLevelId)))
            ->when($this->selectedGroupId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('academic_group_id', $this->selectedGroupId)))
            ->when($this->selectedStudentId, fn ($q) => $q->where('student_id', $this->selectedStudentId))
            ->latest()
            ->paginate(20);

        $pendingApprovals = ReportCard::with(['student.user', 'configuration.academicLevel'])
            ->where('school_id', getSchoolId())
            ->where('status', 'submitted')
            ->when($this->selectedLevelId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('academic_level_id', $this->selectedLevelId)))
            ->when($this->selectedGroupId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('academic_group_id', $this->selectedGroupId)))
            ->when($this->selectedStudentId, fn ($q) => $q->where('student_id', $this->selectedStudentId))
            ->latest()
            ->paginate(20, ['*'], 'pendingPage');

        $gradeScales = GradeScale::where('school_id', getSchoolId())
            ->when($this->selectedLevelId, fn ($q) => $q->where(function ($q2) {
                $q2->where('academic_level_id', $this->selectedLevelId)
                    ->orWhere(fn ($q3) => $q3->whereNull('academic_level_id')->where('is_default', true));
            }))
            ->orderBy('min_score')
            ->get();

        $weightings = ScoreWeighting::where('school_id', getSchoolId())
            ->when($this->selectedLevelId, fn ($q) => $q->where(function ($q2) {
                $q2->where('academic_level_id', $this->selectedLevelId)
                    ->orWhere(fn ($q3) => $q3->whereNull('academic_level_id')->where('is_default', true));
            }))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.administrator.report-card-management', compact(
            'years',
            'periods',
            'levels',
            'groups',
            'students',
            'configurations',
            'reportCards',
            'pendingApprovals',
            'gradeScales',
            'weightings'
        ));
    }
}
