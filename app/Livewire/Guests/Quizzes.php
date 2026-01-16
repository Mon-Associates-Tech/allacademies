<?php

namespace App\Livewire\Guests;

use App\Models\AcademicSubject as Subject;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Quizzes extends Component
{
    use WithPagination;

    public $academicGroup;

    public $academicLevel;

    public $academicSubject;

    public $search = '';

    public $subject = '';

    public $difficulty = '';

    public $status = 'available'; // 'available', 'completed', 'all'

    public function mount($academicGroup = null, $academicLevel = null, $academicSubject = null)
    {
        $this->academicGroup = $academicGroup;
        $this->academicLevel = $academicLevel;
        $this->academicSubject = $academicSubject;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function takeQuiz($quizId)
    {
        return redirect()->route('quizzes.take', $quizId);
    }

    public function render()
    {
        $query = Quiz::with(['academicSubject'])
            ->when($this->search, function ($q) {
                return $q->where('title', 'like', '%'.$this->search.'%');
            })
            ->when($this->subject, function ($q) {
                return $q->where('academic_subject_id', $this->subject);
            })
            ->latest();

        return redirect()->route('guests.quizzes.index');

        return to_route('quizzes.index', [
            'quizzes' => $query->paginate(10),
            'subjects' => Subject::all(),
            'currentTeam' => Auth::user()->currentTeam,
            'academicSubject' => Subject::find($this->subject),
        ]);
    }
}
