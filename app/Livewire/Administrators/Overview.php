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
    public function render()
    {
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
        ]);
    }
}
