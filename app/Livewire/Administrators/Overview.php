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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Overview extends Component
{
    public $search = '';
    public $showAllUsers = false;

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

    public function impersonateUser($userId)
    {
        $user = User::findOrFail($userId);

        // Check if current user can impersonate
        if (!Auth::user()->canImpersonate()) {
            session()->flash('error', 'You do not have permission to impersonate users.');
            return;
        }

        // Check if target user can be impersonated
        if (!$user->canBeImpersonated()) {
            session()->flash('error', 'This user cannot be impersonated.');
            return;
        }

        return redirect()->route('impersonate', $userId);
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);

        session()->flash('message', $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.');
    }

    public function render()
    {
        $cards = [
            [
                'label' => 'Total Users',
                'value' => User::count(),
                'change' => '+' . User::whereDate('created_at', today())->count() . ' today',
                'changeType' => 'positive',
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
                'change' => Book::where('status', 'approved')->count() . ' approved',
                'changeType' => 'neutral',
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
                'change' => 'Requires attention',
                'changeType' => Book::where('status', 'pending')->count() > 0 ? 'negative' : 'positive',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>',
                'bgLight' => 'bg-yellow-50',
                'bgDark' => 'dark:bg-yellow-900/20',
                'textColor' => 'text-yellow-600',
                'darkTextColor' => 'dark:text-yellow-400'
            ],
            [
                'label' => 'Active Users',
                'value' => User::where('last_seen_at', '>=', now()->subDays(7))->count(),
                'change' => User::where('is_online', true)->count() . ' online now',
                'changeType' => 'positive',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.07 0a5 5 0 010 7.07M12 12a1 1 0 110-2 1 1 0 010 2z"/>
                          </svg>',
                'bgLight' => 'bg-purple-50',
                'bgDark' => 'dark:bg-purple-900/20',
                'textColor' => 'text-purple-600',
                'darkTextColor' => 'dark:text-purple-400'
            ]
        ];

        // System health indicators
        $systemHealth = [
            'database' => $this->checkDatabaseConnection(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
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
            'totalLibrarians' => Librarian::count(),
            'totalAuthors' => Author::count(),
        ];

        // Enhanced user query with search and pagination
        $recentUsersQuery = User::with(['roles', 'primaryRole'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->whereNotNull('email_verified_at')
            ->latest();

        $recentUsers = $this->showAllUsers
            ? $recentUsersQuery->take(20)->get()
            : $recentUsersQuery->take(5)->get();

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
            'systemHealth' => $systemHealth,
        ]);
    }

    private function checkDatabaseConnection(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Connected'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Connection failed'];
        }
    }

    private function checkStorageHealth(): array
    {
        $freeSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usedPercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usedPercentage > 90) {
            return ['status' => 'warning', 'message' => 'Low disk space'];
        } elseif ($usedPercentage > 95) {
            return ['status' => 'error', 'message' => 'Critical disk space'];
        }

        return ['status' => 'healthy', 'message' => 'Sufficient space'];
    }

    private function checkCacheHealth(): array
    {
        try {
            cache()->put('health_check', 'test', 60);
            $value = cache()->get('health_check');

            if ($value === 'test') {
                return ['status' => 'healthy', 'message' => 'Working'];
            }

            return ['status' => 'warning', 'message' => 'Issues detected'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache failed'];
        }
    }

    public function toggleShowAllUsers()
    {
        $this->showAllUsers = !$this->showAllUsers;
    }
}
