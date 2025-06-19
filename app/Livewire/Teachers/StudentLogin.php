<?php

namespace App\Livewire\Teachers;

use App\Models\UserLogin;
use Livewire\Component;
use Livewire\WithPagination;

class StudentLogin extends Component
{
    use WithPagination;

    public $searchTerm = '';

    public function render()
    {
        $teacher = auth()->user();

        $activities = UserLogin::with('user')
            ->whereHas('user', function($query) use ($teacher) {
                $query->whereHas('academicGroup', function($q) use ($teacher) {
                    $q->where('academic_group_id', $teacher->academic_group_id)
                      ->where('academic_level_id', $teacher->academic_level_id);
                });
            })
            ->when($this->searchTerm, function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                })->orWhere('action', 'like', '%' . $this->searchTerm . '%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.teachers.students-login', [
            'activities' => $activities
        ]);
    }
}
