<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Book;
use App\Models\StudentGroup;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\BookApproval;
use App\Models\Librarian;
use App\Models\Author;

class Overview extends Component
{

    public function approveBook($bookId)
    {
        $book = Book::findOrFail($bookId);
        $book->update(['status' => 'approved']);
        $this->emit('bookApproved');
    }

    public function rejectBook($bookId)
    {
        $book = Book::findOrFail($bookId);
        $book->update(['status' => 'rejected']);
        $this->emit('bookRejected');
    }

    public function approveAll()
    {
        Book::where('status', 'pending')->update(['status' => 'approved']);
        $this->emit('allBooksApproved');
    }
    public function render()
    {

        $cards = [
            [
                'label' => 'Total Users',
                'value' => User::count(),
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                          </svg>',
                'bgLight' => 'bg-blue-50',
                'bgDark' => 'dark:bg-blue-900/20',
                'textColor' => 'text-blue-600',
                'darkTextColor' => 'dark:text-blue-400'
            ],
            [
                'label' => 'Total Books',
                'value' => Book::count(),
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                          </svg>',
                'bgLight' => 'bg-green-50',
                'bgDark' => 'dark:bg-green-900/20',
                'textColor' => 'text-green-600',
                'darkTextColor' => 'dark:text-green-400'
            ],
            [
                'label' => 'Pending Approvals',
                'value' => Book::where('status', 'pending')->count(),
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>',
                'bgLight' => 'bg-yellow-50',
                'bgDark' => 'dark:bg-yellow-900/20',
                'textColor' => 'text-yellow-600',
                'darkTextColor' => 'dark:text-yellow-400'
            ],
            [
                'label' => 'Active Users',
                'value' => User::where('last_seen_at', '>=', now()->subDays(7))->count(),
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                          </svg>',
                'bgLight' => 'bg-purple-50',
                'bgDark' => 'dark:bg-purple-900/20',
                'textColor' => 'text-purple-600',
                'darkTextColor' => 'dark:text-purple-400'
            ]
        ];


        $statistics = [
            'totalUsers' => User::count(),
            'totalStudents' => Student::count(),
            'totalTeachers' => Teacher::count(),
            'totalGroups' => StudentGroup::count(),
            'totalBooks' => Book::count(),
            'pendingApprovals' => BookApproval::where('status', 'pending')->count(),
            'activeBorrowings' => BookBorrowing::where('status', 'borrowed')->count(),
            'activeSubscriptions' => BookSubscription::where('status', 'active')->count(),
            // Adding new statistics for Librarians and Authors
            'totalLibrarians' => Librarian::count(),
            'totalAuthors' => Author::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $pendingApprovals = BookApproval::with(['book', 'librarian.user'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.administrators.overview', [
            'statistics' => $statistics,
            'recentUsers' => $recentUsers,
            'pendingApprovals' => $pendingApprovals,
            'cards' => $cards,
        ]);
    }
}
