<?php

namespace App\Livewire\School;

use App\Models\AcademicYear;
use App\Models\AcademicPeriod;
use App\Models\GradeScale;
use App\Models\School;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SchoolConfigurationManager extends Component
{
    use WithFileUploads;

    public School $school;
    public $activeSection = 'basic-info';

    // Basic Information
    public $name;
    public $code;
    public $email;
    public $phone;
    public $website;
    public $logo;
    public $newLogo;

    // Address Information
    public $address;
    public $city;
    public $state;
    public $country;
    public $postal_code;

    // School Details
    public $type;
    public $description;
    public $student_capacity;
    public $timezone;
    public $currency;

    // Branding & Report Headers
    public $report_header;
    public $report_footer;
    public $letterhead;
    public $newLetterhead;
    public $school_motto;
    public $school_colors = ['primary' => '#4F46E5', 'secondary' => '#10B981'];

    // Academic Years
    public $academicYears = [];
    public $currentAcademicYear = null;
    public $showAcademicYearModal = false;
    public $editingYearId = null;

    public $yearName;
    public $yearStartDate;
    public $yearEndDate;
    public $yearStatus = 'upcoming';

    // Academic Periods/Terms
    public $academicPeriods = [];
    public $showPeriodModal = false;
    public $editingPeriodId = null;

    public $periodTitle;
    public $periodType = 'term';
    public $periodAcademicYearId;
    public $periodSequence = 1;
    public $periodStartDate;
    public $periodEndDate;
    public $periodStatus = 'upcoming';
    public $periodDescription;
    public $registrationStart;
    public $registrationEnd;
    public $examStart;
    public $examEnd;

    // Grade Scales
    public $gradeScales = [];
    public $showGradeScaleModal = false;
    public $editingGradeScaleId = null;

    public $gradeName;
    public $gradeMinScore;
    public $gradeMaxScore;
    public $gradePoint;
    public $gradeDescription;
    public $gradeColor = '#10B981';

    // School Types
    public $schoolTypes = [
        'primary' => 'Primary School',
        'secondary' => 'Secondary School',
        'high_school' => 'High School',
        'college' => 'College',
        'university' => 'University',
        'technical' => 'Technical Institute',
        'vocational' => 'Vocational School',
        'mixed' => 'Mixed/Combined'
    ];

    public $timezones = [
        'UTC' => 'UTC',
        'America/New_York' => 'Eastern Time',
        'America/Chicago' => 'Central Time',
        'America/Denver' => 'Mountain Time',
        'America/Los_Angeles' => 'Pacific Time',
        'Europe/London' => 'GMT',
        'Europe/Paris' => 'Central European Time',
        'Asia/Dubai' => 'GST',
        'Asia/Kolkata' => 'IST',
        'Asia/Tokyo' => 'JST',
        'Australia/Sydney' => 'AEST',
    ];

    public $currencies = [
        'USD' => 'US Dollar ($)',
        'EUR' => 'Euro (€)',
        'GBP' => 'British Pound (£)',
        'JPY' => 'Japanese Yen (¥)',
        'AUD' => 'Australian Dollar (A$)',
        'CAD' => 'Canadian Dollar (C$)',
        'INR' => 'Indian Rupee (₹)',
        'AED' => 'UAE Dirham (د.إ)',
        'SAR' => 'Saudi Riyal (﷼)',
        'NGN' => 'Nigerian Naira (₦)',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'website' => 'nullable|url|max:255',
        'newLogo' => 'nullable|image|max:2048',
        'newLetterhead' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->school = auth()->user()->school;
        $this->loadSchoolData();
        $this->loadAcademicYears();
        $this->loadAcademicPeriods();
        $this->loadGradeScales();
    }

    public function loadSchoolData()
    {
        $this->name = $this->school->name;
        $this->code = $this->school->code;
        $this->email = $this->school->email;
        $this->phone = $this->school->phone;
        $this->website = $this->school->website;
        $this->logo = $this->school->logo;

        $this->address = $this->school->address;
        $this->city = $this->school->city;
        $this->state = $this->school->state;
        $this->country = $this->school->country;
        $this->postal_code = $this->school->postal_code;

        $this->type = $this->school->type;
        $this->description = $this->school->description;
        $this->student_capacity = $this->school->student_capacity;
        $this->timezone = $this->school->timezone ?? 'UTC';
        $this->currency = $this->school->currency ?? 'USD';

        // Load branding settings from school settings array
        $settings = $this->school->settings ?? [];
        $this->report_header = $settings['report_header'] ?? '';
        $this->report_footer = $settings['report_footer'] ?? '';
        $this->letterhead = $settings['letterhead'] ?? '';
        $this->school_motto = $settings['school_motto'] ?? '';
        $this->school_colors = $settings['school_colors'] ?? ['primary' => '#4F46E5', 'secondary' => '#10B981'];
    }

    public function loadAcademicYears()
    {
        $this->academicYears = AcademicYear::where('school_id', $this->school->id)
            ->orderBy('start_date', 'desc')
            ->get();

        $this->currentAcademicYear = $this->academicYears->where('is_current', true)->first();
    }

    public function loadAcademicPeriods()
    {
        $this->academicPeriods = AcademicPeriod::where('school_id', $this->school->id)
            ->with('academicYear')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function loadGradeScales()
    {
        $this->gradeScales = GradeScale::where('school_id', $this->school->id)
            ->orderBy('min_score', 'desc')
            ->get();
    }

    public function setActiveSection($section)
    {
        $this->activeSection = $section;
    }

    public function saveBasicInfo()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        $this->school->update([
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'type' => $this->type,
            'description' => $this->description,
            'student_capacity' => $this->student_capacity,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
        ]);

        if ($this->newLogo) {
            // Delete old logo
            if ($this->logo) {
                Storage::disk('public')->delete($this->logo);
            }

            $logoPath = $this->newLogo->store('school-logos', 'public');
            $this->school->update(['logo' => $logoPath]);
            $this->logo = $logoPath;
            $this->newLogo = null;
        }

        session()->flash('success', 'School information updated successfully!');
    }

    public function saveBrandingSettings()
    {
        $settings = $this->school->settings ?? [];

        $settings['report_header'] = $this->report_header;
        $settings['report_footer'] = $this->report_footer;
        $settings['school_motto'] = $this->school_motto;
        $settings['school_colors'] = $this->school_colors;

        if ($this->newLetterhead) {
            // Delete old letterhead
            if (!empty($settings['letterhead'])) {
                Storage::disk('public')->delete($settings['letterhead']);
            }

            $letterheadPath = $this->newLetterhead->store('school-letterheads', 'public');
            $settings['letterhead'] = $letterheadPath;
            $this->letterhead = $letterheadPath;
            $this->newLetterhead = null;
        }

        $this->school->update(['settings' => $settings]);
        session()->flash('success', 'Branding settings updated successfully!');
    }

    // Academic Year Methods
    public function createAcademicYear()
    {
        $this->resetAcademicYearForm();
        $this->showAcademicYearModal = true;
    }

    public function editAcademicYear($yearId)
    {
        $year = AcademicYear::findOrFail($yearId);

        $this->editingYearId = $yearId;
        $this->yearName = $year->name;
        $this->yearStartDate = $year->start_date->format('Y-m-d');
        $this->yearEndDate = $year->end_date->format('Y-m-d');
        $this->yearStatus = $year->status ?? 'active';

        $this->showAcademicYearModal = true;
    }

    public function saveAcademicYear()
    {
        $this->validate([
            'yearName' => 'required|string|max:255',
            'yearStartDate' => 'required|date',
            'yearEndDate' => 'required|date|after:yearStartDate',
        ]);

        $data = [
            'school_id' => $this->school->id,
            'name' => $this->yearName,
            'start_date' => $this->yearStartDate,
            'end_date' => $this->yearEndDate,
            'status' => $this->yearStatus,
        ];

        if ($this->editingYearId) {
            AcademicYear::findOrFail($this->editingYearId)->update($data);
            session()->flash('success', 'Academic year updated successfully!');
        } else {
            AcademicYear::create($data);
            session()->flash('success', 'Academic year created successfully!');
        }

        $this->loadAcademicYears();
        $this->showAcademicYearModal = false;
    }

    public function deleteAcademicYear($yearId)
    {
        AcademicYear::findOrFail($yearId)->delete();
        $this->loadAcademicYears();
        session()->flash('success', 'Academic year deleted successfully!');
    }

    public function setCurrentAcademicYear($yearId)
    {
        AcademicYear::where('school_id', $this->school->id)->update(['is_current' => false]);
        AcademicYear::findOrFail($yearId)->update(['is_current' => true]);

        $this->loadAcademicYears();
        session()->flash('success', 'Current academic year updated!');
    }

    // Academic Period Methods
    public function createPeriod()
    {
        $this->resetPeriodForm();
        $this->showPeriodModal = true;
    }

    public function editPeriod($periodId)
    {
        $period = AcademicPeriod::findOrFail($periodId);

        $this->editingPeriodId = $periodId;
        $this->periodTitle = $period->title;
        $this->periodType = $period->type;
        $this->periodAcademicYearId = $period->academic_year_id;
        $this->periodSequence = $period->sequence;
        $this->periodStartDate = $period->start_date->format('Y-m-d');
        $this->periodEndDate = $period->end_date->format('Y-m-d');
        $this->periodStatus = $period->status;
        $this->periodDescription = $period->description;
        $this->registrationStart = $period->registration_start?->format('Y-m-d');
        $this->registrationEnd = $period->registration_end?->format('Y-m-d');
        $this->examStart = $period->exam_start?->format('Y-m-d');
        $this->examEnd = $period->exam_end?->format('Y-m-d');

        $this->showPeriodModal = true;
    }

    public function savePeriod()
    {
        $this->validate([
            'periodTitle' => 'required|string|max:255',
            'periodType' => 'required|in:semester,term,quarter,trimester,session',
            'periodStartDate' => 'required|date',
            'periodEndDate' => 'required|date|after:periodStartDate',
        ]);

        $data = [
            'school_id' => $this->school->id,
            'academic_year_id' => $this->periodAcademicYearId,
            'title' => $this->periodTitle,
            'type' => $this->periodType,
            'sequence' => $this->periodSequence,
            'start_date' => $this->periodStartDate,
            'end_date' => $this->periodEndDate,
            'status' => $this->periodStatus,
            'description' => $this->periodDescription,
            'registration_start' => $this->registrationStart,
            'registration_end' => $this->registrationEnd,
            'exam_start' => $this->examStart,
            'exam_end' => $this->examEnd,
        ];

        if ($this->editingPeriodId) {
            AcademicPeriod::findOrFail($this->editingPeriodId)->update($data);
            session()->flash('success', 'Academic period updated successfully!');
        } else {
            AcademicPeriod::create($data);
            session()->flash('success', 'Academic period created successfully!');
        }

        $this->loadAcademicPeriods();
        $this->showPeriodModal = false;
    }

    public function deletePeriod($periodId)
    {
        AcademicPeriod::findOrFail($periodId)->delete();
        $this->loadAcademicPeriods();
        session()->flash('success', 'Academic period deleted successfully!');
    }

    // Grade Scale Methods
    public function createGradeScale()
    {
        $this->resetGradeScaleForm();
        $this->showGradeScaleModal = true;
    }

    public function editGradeScale($gradeId)
    {
        $grade = GradeScale::findOrFail($gradeId);

        $this->editingGradeScaleId = $gradeId;
        $this->gradeName = $grade->grade;
        $this->gradeMinScore = $grade->min_score;
        $this->gradeMaxScore = $grade->max_score;
        $this->gradePoint = $grade->grade_point;
        $this->gradeDescription = $grade->description;
        $this->gradeColor = $grade->color ?? '#10B981';

        $this->showGradeScaleModal = true;
    }

    public function saveGradeScale()
    {
        $this->validate([
            'gradeName' => 'required|string|max:10',
            'gradeMinScore' => 'required|numeric|min:0|max:100',
            'gradeMaxScore' => 'required|numeric|min:0|max:100|gte:gradeMinScore',
            'gradePoint' => 'nullable|numeric|min:0|max:5',
        ]);

        $data = [
            'school_id' => $this->school->id,
            'grade' => $this->gradeName,
            'min_score' => $this->gradeMinScore,
            'max_score' => $this->gradeMaxScore,
            'grade_point' => $this->gradePoint,
            'description' => $this->gradeDescription,
            'color' => $this->gradeColor,
        ];

        if ($this->editingGradeScaleId) {
            GradeScale::findOrFail($this->editingGradeScaleId)->update($data);
            session()->flash('success', 'Grade scale updated successfully!');
        } else {
            GradeScale::create($data);
            session()->flash('success', 'Grade scale created successfully!');
        }

        $this->loadGradeScales();
        $this->showGradeScaleModal = false;
    }

    public function deleteGradeScale($gradeId)
    {
        GradeScale::findOrFail($gradeId)->delete();
        $this->loadGradeScales();
        session()->flash('success', 'Grade scale deleted successfully!');
    }

    private function resetAcademicYearForm()
    {
        $this->editingYearId = null;
        $this->yearName = '';
        $this->yearStartDate = '';
        $this->yearEndDate = '';
        $this->yearStatus = 'upcoming';
    }

    private function resetPeriodForm()
    {
        $this->editingPeriodId = null;
        $this->periodTitle = '';
        $this->periodType = 'term';
        $this->periodAcademicYearId = null;
        $this->periodSequence = 1;
        $this->periodStartDate = '';
        $this->periodEndDate = '';
        $this->periodStatus = 'upcoming';
        $this->periodDescription = '';
        $this->registrationStart = '';
        $this->registrationEnd = '';
        $this->examStart = '';
        $this->examEnd = '';
    }

    private function resetGradeScaleForm()
    {
        $this->editingGradeScaleId = null;
        $this->gradeName = '';
        $this->gradeMinScore = '';
        $this->gradeMaxScore = '';
        $this->gradePoint = '';
        $this->gradeDescription = '';
        $this->gradeColor = '#10B981';
    }

    public function render()
    {
        return view('livewire.school.school-configuration-manager');
    }
}
