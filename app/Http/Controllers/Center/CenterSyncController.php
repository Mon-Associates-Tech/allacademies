<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\ExamSyncState;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Services\PushAgent;
use App\ExaminationHub\Services\PullAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * CenterSyncController — sync status page.
 *
 * On central instances: shows all centers' sync status with filtering.
 * On center instances: shows local sync status with pull/push buttons.
 */
class CenterSyncController
{
    /**
     * Show the sync status page.
     */
    public function index(Request $request)
    {
        $isCentral = config('center.role') === 'central';

        if ($isCentral) {
            return $this->indexCentral($request);
        }

        return $this->indexCenter($request);
    }

    /**
     * Central instance: show all centers' sync status.
     */
    protected function indexCentral(Request $request)
    {
        // Filters
        $statusFilter = $request->get('status'); // all, active, stale, inactive
        $searchQuery = $request->get('search');
        $sortBy = $request->get('sort', 'last_seen_at'); // last_seen_at, name, backlog, parity_failures
        $sortDir = $request->get('dir', 'desc');

        // Build center query
        $centersQuery = Center::query();

        if ($statusFilter === 'active') {
            $centersQuery->where('is_active', true)
                ->where('last_seen_at', '>=', now()->subHours(24));
        } elseif ($statusFilter === 'stale') {
            $centersQuery->where('is_active', true)
                ->where('last_seen_at', '<', now()->subHours(24));
        } elseif ($statusFilter === 'inactive') {
            $centersQuery->where('is_active', false);
        }

        if ($searchQuery) {
            $centersQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('code', 'like', '%' . $searchQuery . '%');
            });
        }

        // Sort
        if (in_array($sortBy, ['last_seen_at', 'name', 'backlog', 'parity_failures'])) {
            if ($sortBy === 'backlog' || $sortBy === 'parity_failures') {
                // These are computed, sort after fetching
                $centers = $centersQuery->orderBy('name')->get();
                $centerData = $this->buildCenterSyncData($centers);
                $centerData->when($sortBy === 'backlog', function ($c) use ($sortDir) {
                    $c->sortByDesc('backlog');
                    if ($sortDir === 'asc') $c->sortBy('backlog');
                })->when($sortBy === 'parity_failures', function ($c) use ($sortDir) {
                    $c->sortByDesc('parity_failures');
                    if ($sortDir === 'asc') $c->sortBy('parity_failures');
                });
                $centerData = $centerData->values();
            } else {
                $centerData = $this->buildCenterSyncData(
                    $centersQuery->orderBy($sortBy, $sortDir)->get()
                );
            }
        } else {
            $centerData = $this->buildCenterSyncData(
                $centersQuery->orderBy('last_seen_at', 'desc')->get()
            );
        }

        // Summary stats
        $totalCenters = Center::count();
        $activeCenters = Center::where('is_active', true)
            ->where('last_seen_at', '>=', now()->subHours(24))
            ->count();
        $staleCenters = Center::where('is_active', true)
            ->where('last_seen_at', '<', now()->subHours(24))
            ->count();
        $totalBacklog = GeneralExamSubmission::whereRaw('synced_version < sync_version')->count();
        $totalParityFailures = GeneralExamSubmission::where('parity_ok', false)->count();

        // Recent sync runs from all centers (on central, these are push runs ingested)
        $recentSyncRuns = SyncRun::latest()->limit(20)->get();

        // All centers for filter dropdown
        $allCenters = Center::orderBy('name')->get();
        $isCentral = config('center.role') === 'central';


        return view('center-sync.sync.index', compact(
            'centerData',
            'totalCenters',
            'activeCenters',
            'staleCenters',
            'totalBacklog',
            'totalParityFailures',
            'recentSyncRuns',
            'allCenters',
            'statusFilter',
            'searchQuery',
            'sortBy',
            'sortDir',
            'isCentral'
        ));
    }

    /**
     * Build per-center sync data collection.
     */
    protected function buildCenterSyncData(Collection $centers): Collection
    {
        $data = new Collection();

        foreach ($centers as $center) {
            $backlog = GeneralExamSubmission::where('center_id', $center->id)
                ->whereRaw('synced_version < sync_version')
                ->count();

            $parityFailures = GeneralExamSubmission::where('center_id', $center->id)
                ->where('parity_ok', false)
                ->count();

            $totalSubmissions = GeneralExamSubmission::where('center_id', $center->id)->count();
            $totalProctoringLogs = ExamProctoringLog::where('center_id', $center->id)->count();

            $isStale = $center->last_seen_at && $center->last_seen_at->lt(now()->subHours(24));

            $data->push([
                'center' => $center,
                'backlog' => $backlog,
                'parity_failures' => $parityFailures,
                'total_submissions' => $totalSubmissions,
                'total_proctoring_logs' => $totalProctoringLogs,
                'is_stale' => $isStale,
                'last_seen_diff' => $center->last_seen_at ? $center->last_seen_at->diffForHumans() : null,
                'last_pull_diff' => $center->last_pull_at ? $center->last_pull_at->diffForHumans() : null,
                'last_push_diff' => $center->last_push_at ? $center->last_push_at->diffForHumans() : null,
            ]);
        }

        return $data;
    }

    /**
     * Center instance: show local sync status.
     */
    protected function indexCenter(Request $request)
    {
        $isCentral = false;

        // Recent sync runs
        $recentPulls = SyncRun::where('direction', 'pull')
            ->latest()
            ->limit(20)
            ->get();

        $recentPushes = SyncRun::where('direction', 'push')
            ->latest()
            ->limit(20)
            ->get();

        // Per-exam sync state
        $examSyncStates = ExamSyncState::with('exam')->get();

        // Backlog: submissions where synced_version < sync_version
        $backlog = GeneralExamSubmission::whereRaw('synced_version < sync_version')
            ->count();

        // Last pull/push times
        $lastPull = SyncRun::where('direction', 'pull')
            ->orderBy('started_at', 'desc')
            ->first();

        $lastPush = SyncRun::where('direction', 'push')
            ->orderBy('started_at', 'desc')
            ->first();

        // Failed sync runs
        $failedSyncs = SyncRun::where('status', 'failed')
            ->latest()
            ->limit(10)
            ->get();

        return view('center-sync.sync.index', compact(
            'recentPulls',
            'recentPushes',
            'examSyncStates',
            'backlog',
            'lastPull',
            'lastPush',
            'failedSyncs',
            'isCentral'
        ));
    }

    /**
     * Trigger an immediate push sync.
     */
    public function pushNow(Request $request)
    {
        $agent = app(PushAgent::class);
        $result = $agent->push();

        if (isset($result['submissions']['error'])) {
            return back()->with('error', 'Push failed: ' . $result['submissions']['error']);
        }

        return back()->with('success', 'Push completed successfully.');
    }

    /**
     * Trigger an immediate pull sync.
     */
    public function pullNow(Request $request)
    {
        $agent = app(PullAgent::class);
        $result = $agent->pull();

        if (!$result['success']) {
            $errors = $result['errors'] ?? ['Unknown error'];
            return back()->with('error', 'Pull failed: ' . implode(', ', $errors));
        }

        $message = "Pull completed: {$result['pulled']} exam(s) pulled.";
        if (!empty($result['details'])) {
            $successCount = 0;
            foreach ($result['details'] as $detail) {
                if (($detail['status'] ?? '') === 'success') {
                    $successCount++;
                }
            }
            $message .= " {$successCount} exam(s) applied successfully.";
        }

        return back()->with('success', $message);
    }
}
