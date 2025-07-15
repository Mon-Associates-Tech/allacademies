<?php

namespace App\Livewire\Students;

use App\Enums\SubscriptionPackage;
use App\Enums\SubscriptionStatus;
use App\Models\AcademicSubject;
use App\Models\Team;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Courses extends Component
{
    public $showSubjectModal = false;
    public $selectedSubject = null;

    public function showSubjectDetails($subjectId): void
    {
        $student = auth()->user()->student;

        if (!$student) {
            return;
        }

        // Get the subject with detailed information
        $this->selectedSubject = AcademicSubject::with([
            'academicLevel.academicGroup',
            'academicTopics' => function($query) {
                $query->with(['subtopics' => function($subQuery) {
                    $subQuery->orderBy('name');
                }])->orderBy('name');
            },
            'lessons' => function($query) {
                $query->orderBy('title');
            },
            'quizzes',
            'examinations'
        ])->findOrFail($subjectId);

        $this->showSubjectModal = true;
    }

    public function closeSubjectModal()
    {
        $this->showSubjectModal = false;
        $this->selectedSubject = null;
    }

    public function render()
    {
        $currentTeam = Team::query()->find(auth()->user()->current_team_id);
        if (!$currentTeam) {
            $currentTeam = Team::query()->where('owner_id', auth()->id())->first();
        }

        // Get the current student
        $student = auth()->user()->student;

        if (!$student) {
            abort(403, 'Student record not found.');
        }

        // Get student's accessible subjects using the getSubjectDetails method
        $subjectDetails = $student->getSubjectDetails();

        $accessibleSubjects = $subjectDetails['total_accessible'];

        // Filter subjects based on subscription status
        $academicSubjects = $accessibleSubjects->filter(function($subject) {
            return $subject->subscriptions()
                ->where('status', SubscriptionStatus::PAID)
                ->where('expires_at', '>', now())
                ->where('team_id', auth()->user()->current_team_id)
                ->orWhere(function (Builder $query) {
                    $query->where(function (Builder $query) {
                        $query->where('package', SubscriptionPackage::INSTITUTION_FULL)
                            ->where(function (Builder $query) {
                                $query->whereRelation('team', 'owner_id', auth()->id())
                                    ->orWhereHas('team', function (Builder $query) {
                                        $query->whereRelation('members', 'user_id', auth()->id());
                                    });
                            });
                    })->orWhere(function (Builder $query) {
                        $query->where('package', SubscriptionPackage::INDIVIDUAL_FULL)
                            ->whereRelation('subscriber', 'id', auth()->id());
                    });
                })
                ->exists();
        });

        if(!empty($academicSubjects) && $academicSubjects->count()) {
            // Load relationships for display
            $academicSubjects?->load([
                'academicLevel.academicGroup',
                'quizzes',
                'examinations'
            ]);
        } else {
            session()->flash('message', 'You don\'t have any active academic subjects.');
        }


        return view('livewire.students.courses', [
            'academicSubjects' => $academicSubjects,
            'currentTeam' => $currentTeam,
            'student' => $student,
            'subjectDetails' => $subjectDetails
        ]);
    }
}
