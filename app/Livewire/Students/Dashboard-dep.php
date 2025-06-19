<?php

namespace App\Livewire\Students;

use Livewire\Component;
use App\Models\Student;
use App\Models\Book;
use App\Models\Lesson;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\Assessment;
use App\Models\AcademicSubject;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $student;
    public $studentProfile;
    public $subjects = [];
    public $borrowedBooks = [];
    public $subscribedBooks = [];
    public $upcomingLessons = [];
    public $recentAssessments = [];
    public $activeTab = 'overview';

    public function mount()
    {
        // Get the logged-in student
        $this->student = auth()->user();
        $this->studentProfile = Student::where('user_id', $this->student->id)->first();

        if (!$this->studentProfile) {
            return;
        }

        // Get the student's subjects (via student group)
        $this->loadSubjects();

        // Get the student's borrowed books
        $this->loadBorrowedBooks();

        // Get the student's subscribed books
        $this->loadSubscribedBooks();

        // Get upcoming lessons
        $this->loadUpcomingLessons();

        // Get recent assessments
        $this->loadRecentAssessments();
    }

    private function loadSubjects()
    {
        // Get subjects via lessons for student's group
        $this->subjects = AcademicSubject::whereHas('lessons', function($query) {
            $query->where('student_group_id', $this->studentProfile->student_group_id);
        })->withCount(['lessons' => function($query) {
            $query->where('student_group_id', $this->studentProfile->student_group_id);
        }])->get();
    }

    private function loadBorrowedBooks()
    {
        $this->borrowedBooks = BookBorrowing::where('student_id', $this->studentProfile->id)
            ->where(function($query) {
                $query->where('status', 'borrowed')
                    ->orWhere(function($q) {
                        $q->where('status', 'returned')
                            ->where('return_date', '>=', Carbon::now()->subDays(30));
                    });
            })
            ->with('book', 'book.author', 'book.author.user', 'book.bookCategory')
            ->orderBy('borrow_date', 'desc')
            ->get();
    }

    private function loadSubscribedBooks()
    {
        // Get directly subscribed books
        $individualSubscriptions = BookSubscription::where('student_id', $this->studentProfile->id)
            ->where('status', 'active')
            ->with('book', 'book.author', 'book.author.user', 'book.bookCategory')
            ->get();

        // Get books subscribed via student group
        $groupSubscriptions = $this->studentProfile->studentGroup->groupBookSubscriptions()
            ->where('status', 'active')
            ->with('book', 'book.author', 'book.author.user', 'book.bookCategory')
            ->get();

        // Combine both subscription types
        $this->subscribedBooks = $individualSubscriptions->merge($groupSubscriptions);
    }

    private function loadUpcomingLessons()
    {
        $this->upcomingLessons = Lesson::where('student_group_id', $this->studentProfile->student_group_id)
            ->where('date', '>=', Carbon::now())
            ->with('teacher', 'teacher.user', 'subject')
            ->orderBy('date')
            ->take(5)
            ->get();
    }

    private function loadRecentAssessments()
    {
        $this->recentAssessments = Assessment::where('student_id', $this->studentProfile->id)
            ->with('book', 'book.author', 'book.author.user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.students.dashboard');
    }
}
