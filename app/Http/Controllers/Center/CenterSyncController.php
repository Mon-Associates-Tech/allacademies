<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\ExamSyncState;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Jobs\SyncPushJob;
use App\ExaminationHub\Jobs\SyncPullJob;
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
     * Trigger an immediate push sync via a background job.
     */
    public function pushNow(Request $request)
    {
        SyncPushJob::dispatch();
        return back()->with('success', 'Push sync started — results will be pushed in the background.');
    }

    /**
     * Trigger an immediate pull sync via a background job.
     */
    public function pullNow(Request $request)
    {
        SyncPullJob::dispatch();
        return back()->with('success', 'Pull sync started — exam packages will be pulled in the background.');
    }

    /**
     * Show details of an applied exam package on a center instance.
     *
     * Centers do not store the raw package payload, so we show the applied
     * exam data from local tables instead.
     */
    public function examDetail(Request $request, $examUuid)
    {
        $exam = \App\ExaminationHub\Models\GeneralExam::where('uuid', $examUuid)->first();

        if (!$exam) {
            return redirect()
                ->route('center-sync.sync.index')
                ->with('error', 'Exam not found on this center.');
        }

        $exam->load([
            'sections.questions',
            'configuredParticipants',
            'academicSubject.academicLevel.academicGroup',
        ]);

        $syncState = \App\ExaminationHub\Models\ExamSyncState::where('exam_uuid', $examUuid)->first();

        return view('center-sync.sync.exam-detail', compact('exam', 'syncState'));
    }
}
