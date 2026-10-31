<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Models\ExamSyncState;
use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Services\PushAgent;
use App\ExaminationHub\Services\PullAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CenterSyncController — center admin sync page.
 *
 * Shows last pull, backlog, per-exam applied revision, and errors.
 * Provides "Sync now" button that dispatches the push job.
 */
class CenterSyncController
{
    /**
     * Show the center sync status page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

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
        $backlog = \App\ExaminationHub\Models\GeneralExamSubmission::whereRaw('synced_version < sync_version')
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
            'failedSyncs'
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

        if (isset($result['error'])) {
            return back()->with('error', 'Pull failed: ' . $result['error']);
        }

        return back()->with('success', 'Pull completed successfully.');
    }
}
