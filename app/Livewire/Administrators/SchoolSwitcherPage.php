<?php

namespace App\Livewire\Administrators;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Livewire\WithPagination;

class SchoolSwitcherPage extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $currentSchool = null;

    protected $queryString = ['search', 'page'];

    public function mount(): void
    {
        if (!auth()->user()->hasAnyRole(['admin', 'superadmin', 'owner'])) {
            abort(403);
        }

        try {
            $this->currentSchool = getCurrentSchoolContext();
        } catch (\Exception $e) {
            $this->currentSchool = null;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function switchToSchool($schoolId): void
    {
        if (!auth()->user()->canAccessCrossSchool()) {
            abort(403);
        }

        $school = School::find($schoolId);

        if (!$school) {
            session()->flash('error', 'School not found.');

            return;
        }

        // Set the current school in session
        session()->put('current_school_id', $schoolId);

        // Keep the session alive
        session()->regenerate(false);

        app()->instance('current_school', $school);

        $this->currentSchool = $school;

        session()->flash('success', "Switched to {$school->name}");

        // Don't redirect, stay on the page
        // return redirect()->route('admin.school-switcher');
    }

    public function showAllSchools(): void
    {
        if (!auth()->user()->canAccessCrossSchool()) {
            abort(403);
        }

        session()->forget('current_school_id');

        // Keep the session alive
        session()->regenerate(false);

        if (app()->bound('current_school')) {
            app()->forgetInstance('current_school');
        }

        app()->instance('current_school', null);

        $this->currentSchool = null;

        session()->flash('success', 'Now viewing all schools');

        // Don't redirect, stay on the page
        // return redirect()->route('admin.school-switcher');
    }

    public function viewSchoolDetails($schoolId): RedirectResponse
    {
        return redirect()->route('admin.school-details', $schoolId);
    }

    public function getSchoolsProperty()
    {
        return School::active()
            ->withValidSubscription()
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.administrators.school-switcher-page', [
            'schools' => $this->schools,
        ]);
    }
}
