<?php

namespace App\Livewire\Administrators;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SchoolSwitcher extends Component
{
    public $currentSchool = null;
    public $schools = [];
    public $showAllSchools = true;
    public $showExpanded = false;

    protected $listeners = ['refreshSchoolContext' => 'refreshContext'];

    public function mount()
    {
        // Check if user has permission to switch schools
        if (!$this->canSwitchSchools()) {
           abort(403, 'Unauthorized to switch schools');
        }

        $this->loadSchools();
        $this->currentSchool = $this->getCurrentSchoolFromSession();
        $this->showAllSchools = is_null($this->currentSchool);
    }

    public function loadSchools()
    {
        $user = Auth::user();

        // Get all schools for owners/super admins
        if ($user->hasRole('owner') || $user->isSuperAdmin()) {
            $this->schools = School::active()
                ->withValidSubscription()
                ->orderBy('name')
                ->get();
        }
    }

    public function handleSchoolChange($schoolId)
    {
        if (!$this->canSwitchSchools()) {
            return;
        }

        if (empty($schoolId) || $schoolId === '' || $schoolId === 'all') {
            // Switch to all schools view
            $this->showAllSchools();
        } else {
            // Switch to specific school
            $this->switchToSchool($schoolId);
        }
    }

    public function switchToSchool($schoolId)
    {
        if (!$this->canSwitchSchools()) {
            return;
        }

        $school = School::find($schoolId);

        if (!$school) {
            session()->flash('error', 'School not found.');
            return;
        }

        // Set the current school in session
        session()->put('current_school_id', $schoolId);
        app()->instance('current_school', $school);

        $this->currentSchool = $school;
        $this->showAllSchools = false;

        session()->flash('success', "Switched to {$school->name}");

        // Emit event to refresh other components - this will trigger page refresh
        $this->dispatch('school-switched', [
            'schoolId' => $schoolId,
            'schoolName' => $school->name,
            'reload' => true
        ]);
    }

    public function showAllSchools()
    {
        if (!$this->canSwitchSchools()) {
            return;
        }

        // Clear the current school from session
        session()->forget('current_school_id');

        if (app()->bound('current_school')) {
            app()->forgetInstance('current_school');
        }

        $this->currentSchool = null;
        $this->showAllSchools = true;

        session()->flash('success', 'Now viewing all schools');

        // Emit event to refresh other components - this will trigger page refresh
        $this->dispatch('school-switched', [
            'schoolId' => null,
            'schoolName' => 'All Schools',
            'reload' => true
        ]);
    }

    public function refreshContext()
    {
        $this->currentSchool = $this->getCurrentSchoolFromSession();
        $this->showAllSchools = is_null($this->currentSchool);
    }

    public function canSwitchSchools(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('owner') || $user->isSuperAdmin());
    }

    private function getCurrentSchoolFromSession()
    {
        $schoolId = session('current_school_id');

        if ($schoolId) {
            $school = School::find($schoolId);
            if ($school) {
                app()->instance('current_school', $school);
                return $school;
            }
        }

        return null;
    }

    public function getStatsProperty()
    {
        if (!$this->canSwitchSchools()) {
            return [];
        }

        try {
            if ($this->showAllSchools) {
                // Global stats across all schools
                return [
                    'total_schools' => School::active()->count(),
                    'total_users' => User::active()->count(),
                    'total_students' => \App\Models\Student::crossSchool()->active()->count(),
                    'total_teachers' => \App\Models\Teacher::crossSchool()->active()->count(),
                ];
            }

            // School-specific stats
            if ($this->currentSchool) {
                return $this->currentSchool->getStats();
            }
        } catch (\Exception $e) {
            // If there's an error with cross-school queries, return basic stats
            return [
                'total_schools' => School::active()->count(),
                'error' => 'Unable to load detailed stats'
            ];
        }

        return [];
    }

    public function render()
    {
        return view('livewire.administrators.school-switcher');
    }
}
