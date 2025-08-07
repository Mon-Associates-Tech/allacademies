<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\AcademicSubject;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\Book;
use App\Models\User;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentTeam = Team::query()->find(auth()->user()->current_team_id);
        if (!$currentTeam) {
            $currentTeam = Team::query()->where('owner_id', auth()->id())->first();
        }

        // Get filter options
        $academicGroups = AcademicGroup::with('academicLevels')->orderBy('name')->get();
        $academicLevels = AcademicLevel::with('academicGroup')->orderBy('name')->get();

        // Build the query with filters
        $query = AcademicSubject::query()
            ->with(['academicLevel.academicGroup', 'quizzes', 'examinations'])
            ->whereHas('subscriptions', function (Builder $query) {
                $query->where('status', SubscriptionStatus::PAID)
                    ->where('expires_at', '>', now())
                    ->where('team_id', auth()->user()->current_team_id)
                    ->where(function (Builder $query) {
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
                    });
            });

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('academicLevel', function (Builder $levelQuery) use ($searchTerm) {
                        $levelQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('academicLevel.academicGroup', function (Builder $groupQuery) use ($searchTerm) {
                        $groupQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Apply academic group filter
        if ($request->filled('academic_group')) {
            $query->whereHas('academicLevel.academicGroup', function (Builder $q) use ($request) {
                $q->where('id', $request->academic_group);
            });
        }

        // Apply academic level filter
        if ($request->filled('academic_level')) {
            $query->where('academic_level_id', $request->academic_level);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'group':
                $query->join('academic_levels', 'academic_subjects.academic_level_id', '=', 'academic_levels.id')
                    ->join('academic_groups', 'academic_levels.academic_group_id', '=', 'academic_groups.id')
                    ->orderBy('academic_groups.name', $sortOrder)
                    ->select('academic_subjects.*');
                break;
            case 'level':
                $query->join('academic_levels', 'academic_subjects.academic_level_id', '=', 'academic_levels.id')
                    ->orderBy('academic_levels.name', $sortOrder)
                    ->select('academic_subjects.*');
                break;
            case 'quizzes_count':
                $query->withCount('quizzes')->orderBy('quizzes_count', $sortOrder);
                break;
            case 'examinations_count':
                $query->withCount('examinations')->orderBy('examinations_count', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
                break;
        }

        $academicSubjects = $query->latest('id')->paginate(12);

        // Enhanced analytics data
        $dashboardStats = $this->getDashboardStats();
        $recentActivity = $this->getRecentActivity();
        $upcomingEvents = $this->getUpcomingEvents();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('dashboard', [
            'academicSubjects' => $academicSubjects,
            'currentTeam' => $currentTeam,
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'dashboardStats' => $dashboardStats,
            'recentActivity' => $recentActivity,
            'upcomingEvents' => $upcomingEvents,
            'performanceMetrics' => $performanceMetrics,
            'filters' => [
                'search' => $request->search,
                'academic_group' => $request->academic_group,
                'academic_level' => $request->academic_level,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    private function getDashboardStats()
    {
        $user = auth()->user();
        $teamId = $user->current_team_id;

        return [
            'total_users' => User::whereHas('joinedTeams', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })->count(),
            'active_subscriptions' => BookSubscription::where('status', 'paid')
                ->whereHas('user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })->count(),
            'books_borrowed_today' => BookBorrowing::whereDate('created_at', today())
                ->whereHas('user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })->count(),
            'overdue_books' => BookBorrowing::where('status', 'active')
                ->where('due_date', '<', now())
                ->whereHas('user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })->count(),
        ];
    }

    private function getRecentActivity()
    {
        $teamId = auth()->user()->current_team_id;

        return [
            'recent_borrowings' => BookBorrowing::with(['user', 'book'])
                ->whereHas('user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })
                ->latest()
                ->take(5)
                ->get(),
            'new_subscriptions' => BookSubscription::with(['user', 'book'])
                ->where('status', 'paid')
                ->whereHas('user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    private function getUpcomingEvents()
    {
        return [
            'due_returns' => BookBorrowing::with(['user', 'book'])
                ->where('status', 'active')
                ->whereBetween('due_date', [now(), now()->addDays(7)])
                ->orderBy('due_date')
                ->take(10)
                ->get(),
            'expiring_subscriptions' => BookSubscription::with(['user', 'book'])
                ->where('status', 'paid')
                ->whereBetween('end_date', [now(), now()->addDays(30)])
                ->orderBy('end_date')
                ->take(10)
                ->get(),
        ];
    }

    private function getPerformanceMetrics()
    {
        $startDate = now()->subDays(30);

        return [
            'borrowing_trend' => BookBorrowing::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'popular_books' => Book::withCount(['borrowings' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
                ->orderBy('borrowings_count', 'desc')
                ->take(10)
                ->get(),
            'active_users_trend' => User::select(
                DB::raw('DATE(last_seen_at) as date'),
                DB::raw('COUNT(DISTINCT id) as count')
            )
                ->where('last_seen_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }
}
