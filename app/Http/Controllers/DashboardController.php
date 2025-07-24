<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\AcademicSubject;
use App\Models\Book;
use App\Models\User;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentTeam = Team::query()->find(auth()->user()->current_team_id);
        if (!$currentTeam) {
            $currentTeam = Team::query()->where('owner_id', auth()->id())->first();
        }

        $academicSubjects = AcademicSubject::query()
            ->with('academicLevel.academicGroup')
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
            })
            ->latest('id')
            ->paginate();

        // Enhanced analytics data
        $dashboardStats = $this->getDashboardStats();
        $recentActivity = $this->getRecentActivity();
        $upcomingEvents = $this->getUpcomingEvents();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('dashboard', [
            'academicSubjects' => $academicSubjects,
            'currentTeam' => $currentTeam,
            'dashboardStats' => $dashboardStats,
            'recentActivity' => $recentActivity,
            'upcomingEvents' => $upcomingEvents,
            'performanceMetrics' => $performanceMetrics,
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
            'active_subscriptions' => BookSubscription::where('status', 'active')
                ->whereHas('student.user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })->count(),
            'books_borrowed_today' => BookBorrowing::whereDate('created_at', today())
                ->whereHas('student.user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })->count(),
            'overdue_books' => BookBorrowing::where('status', 'active')
                ->where('due_date', '<', now())
                ->whereHas('student.user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })->count(),
        ];
    }

    private function getRecentActivity()
    {
        $teamId = auth()->user()->current_team_id;

        return [
            'recent_borrowings' => BookBorrowing::with(['student.user', 'book'])
                ->whereHas('student.user', function ($query) use ($teamId) {
                    $query->where('current_team_id', $teamId);
                })
                ->latest()
                ->take(5)
                ->get(),
            'new_subscriptions' => BookSubscription::with(['student.user', 'book'])
                ->where('status', 'active')
                ->whereHas('student.user', function ($query) use ($teamId) {
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
            'due_returns' => BookBorrowing::with(['student.user', 'book'])
                ->where('status', 'active')
                ->whereBetween('due_date', [now(), now()->addDays(7)])
                ->orderBy('due_date')
                ->take(10)
                ->get(),
            'expiring_subscriptions' => BookSubscription::with(['student.user', 'book'])
                ->where('status', 'active')
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
