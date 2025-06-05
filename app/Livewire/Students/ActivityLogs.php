<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Activity;
use App\Models\Assessment;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityLogs extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $activityType = 'all';
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'activityType' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => '']
    ];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActivityType()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function getActivityLogsProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect();

        $logs = collect();

        // Academic Activities
        if (in_array($this->activityType, ['all', 'academic'])) {
            $activities = Activity::forStudent($student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('start_time', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('start_time', '<=', $this->dateTo);
                })
                ->when($this->search, function($query) {
                    $query->where(function($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                    });
                })
                ->with(['subject', 'group'])
                ->get()
                ->map(function($activity) {
                    return [
                        'id' => $activity->id,
                        'type' => 'academic_activity',
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'date' => $activity->start_time,
                        'status' => $activity->status,
                        'metadata' => [
                            'activity_type' => $activity->activity_type,
                            'subject' => $activity->subject?->name,
                            'group' => $activity->is_group_activity ? $activity->group?->name : null,
                            'location' => $activity->location
                        ]
                    ];
                });

            $logs = $logs->merge($activities);
        }

        // Assessments
        if (in_array($this->activityType, ['all', 'assessment'])) {
            $assessments = Assessment::where('student_id', $student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('created_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('created_at', '<=', $this->dateTo);
                })
                ->with('activity.subject')
                ->get()
                ->map(function($assessment) {
                    return [
                        'id' => $assessment->id,
                        'type' => 'assessment',
                        'title' => 'Assessment: ' . ($assessment->activity?->title ?? 'Self Assessment'),
                        'description' => "Score: {$assessment->score}/{$assessment->max_score}",
                        'date' => $assessment->created_at,
                        'status' => 'completed',
                        'metadata' => [
                            'score' => $assessment->score,
                            'max_score' => $assessment->max_score,
                            'percentage' => $assessment->max_score > 0 ? round(($assessment->score / $assessment->max_score) * 100, 2) : 0,
                            'subject' => $assessment->activity?->subject?->name
                        ]
                    ];
                });

            $logs = $logs->merge($assessments);
        }

        // Book Borrowings
        if (in_array($this->activityType, ['all', 'borrowing'])) {
            $borrowings = BookBorrowing::where('student_id', $student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('borrowed_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('borrowed_at', '<=', $this->dateTo);
                })
                ->when($this->search, function($query) {
                    $query->whereHas('book', function($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('author', 'like', '%' . $this->search . '%');
                    });
                })
                ->with('book')
                ->get()
                ->map(function($borrowing) {
                    return [
                        'id' => $borrowing->id,
                        'type' => 'book_borrowing',
                        'title' => 'Borrowed: ' . $borrowing->book->title,
                        'description' => 'Author: ' . $borrowing->book->author,
                        'date' => $borrowing->borrowed_at,
                        'status' => $borrowing->status,
                        'metadata' => [
                            'book_title' => $borrowing->book->title,
                            'author' => $borrowing->book->author,
                            'due_date' => $borrowing->due_date,
                            'returned_at' => $borrowing->returned_at
                        ]
                    ];
                });

            $logs = $logs->merge($borrowings);
        }

        // Book Subscriptions
        if (in_array($this->activityType, ['all', 'subscription'])) {
            $subscriptions = BookSubscription::where('student_id', $student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('subscribed_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('subscribed_at', '<=', $this->dateTo);
                })
                ->when($this->search, function($query) {
                    $query->whereHas('book', function($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('author', 'like', '%' . $this->search . '%');
                    });
                })
                ->with('book')
                ->get()
                ->map(function($subscription) {
                    return [
                        'id' => $subscription->id,
                        'type' => 'book_subscription',
                        'title' => 'Subscribed: ' . $subscription->book->title,
                        'description' => 'Author: ' . $subscription->book->author,
                        'date' => $subscription->subscribed_at,
                        'status' => $subscription->status,
                        'metadata' => [
                            'book_title' => $subscription->book->title,
                            'author' => $subscription->book->author,
                            'expires_at' => $subscription->expires_at
                        ]
                    ];
                });

            $logs = $logs->merge($subscriptions);
        }

        // Sort by date descending and paginate
        return $logs->sortByDesc('date')
                   ->forPage($this->getPage(), 15)
                   ->values();
    }

    public function getTotalLogsProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return 0;

        $count = 0;

        if (in_array($this->activityType, ['all', 'academic'])) {
            $count += Activity::forStudent($student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('start_time', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('start_time', '<=', $this->dateTo);
                })
                ->count();
        }

        if (in_array($this->activityType, ['all', 'assessment'])) {
            $count += Assessment::where('student_id', $student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('created_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('created_at', '<=', $this->dateTo);
                })
                ->count();
        }

        if (in_array($this->activityType, ['all', 'borrowing'])) {
            $count += BookBorrowing::where('student_id', $student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('borrowed_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('borrowed_at', '<=', $this->dateTo);
                })
                ->count();
        }

        if (in_array($this->activityType, ['all', 'subscription'])) {
            $count += BookSubscription::where('student_id', $student->id)
                ->when($this->dateFrom, function($query) {
                    $query->whereDate('subscribed_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($query) {
                    $query->whereDate('subscribed_at', '<=', $this->dateTo);
                })
                ->count();
        }

        return $count;
    }

    public function render()
    {
        return view('livewire.students.activity-logs', [
            'activityLogs' => $this->activityLogs,
            'totalLogs' => $this->totalLogs
        ]);
    }
}