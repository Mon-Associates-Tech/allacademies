<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SyncRun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * CenterDashboardController — multi-center admin dashboard on central.
 *
 * Provides per-center cards with candidates, submitted, completion rate,
 * average score, flagged proctoring, last synced, and backlog size.
 * Also shows parity alerts and cross-center duplicate attempts.
 */
class CenterDashboardController
{
    /**
     * Show the multi-center dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        // Get the selected center filter from session
        $selectedCenterId = $request->get('center_id');

        // For non-admin users, pin to their own center
        if (!$isAdmin && $user->center_id) {
            $selectedCenterId = $user->center_id;
        }

        // Get all centers or filtered by selected center
        $centersQuery = Center::active();
        if ($selectedCenterId) {
            $centersQuery->where('id', $selectedCenterId);
        }
        $centers = $centersQuery->get();

        // Build per-center stats as a Collection so the view can use Collection methods
        $centerStats = new Collection();
        foreach ($centers as $center) {
            $stats = $this->getCenterStats($center);
            $centerStats->push(array_merge($center->toArray(), $stats));
        }

        // Parity alerts: submissions where parity_ok is false
        $parityAlerts = GeneralExamSubmission::where('parity_ok', false)
            ->whereNotNull('parity_checked_at')
            ->with('exam')
            ->latest('parity_checked_at')
            ->limit(50)
            ->get();

        // Cross-center duplicate detection: same index number at multiple centers
        $duplicates = GeneralExamSubmission::select('participant_index_number')
            ->whereNotNull('participant_index_number')
            ->groupBy('participant_index_number')
            ->havingRaw('COUNT(DISTINCT center_id) > 1')
            ->pluck('participant_index_number');

        $duplicateDetails = [];
        if ($duplicates->isNotEmpty()) {
            $duplicateDetails = GeneralExamSubmission::whereIn('participant_index_number', $duplicates)
                ->select('participant_index_number', 'center_id', 'participant_name', 'exam_id')
                ->with('exam')
                ->get()
                ->groupBy('participant_index_number');
        }

        // All centers for the filter dropdown
        $allCenters = Center::active()->orderBy('name')->get();

        return view('center-sync.dashboard', compact(
            'centerStats',
            'parityAlerts',
            'duplicateDetails',
            'allCenters',
            'selectedCenterId',
            'isAdmin'
        ));
    }

    /**
     * Get statistics for a single center.
     */
    protected function getCenterStats(Center $center): array
    {
        $submissions = GeneralExamSubmission::where('center_id', $center->id);

        $totalCandidates = $submissions->count();
        $submitted = $submissions->clone()
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
                GeneralExamSubmission::STATUS_FINAL,
            ])
            ->count();

        $inProgress = $submissions->clone()
            ->where('status', GeneralExamSubmission::STATUS_IN_PROGRESS)
            ->count();

        $completionRate = $totalCandidates > 0 ? round(($submitted / $totalCandidates) * 100, 1) : 0;

        $avgScore = $submissions->clone()
            ->whereNotNull('score')
            ->avg('score');

        // Flagged proctoring: high severity events
        $flaggedProctoring = ExamProctoringLog::where('center_id', $center->id)
            ->where('severity', ExamProctoringLog::SEVERITY_HIGH)
            ->count();

        // Last sync time
        $lastSync = SyncRun::where('direction', 'push')
            ->orderBy('started_at', 'desc')
            ->first();

        // Backlog: submissions where synced_version < sync_version
        $backlog = GeneralExamSubmission::where('center_id', $center->id)
            ->whereRaw('synced_version < sync_version')
            ->count();

        // Parity failures for this center
        $parityFailures = GeneralExamSubmission::where('center_id', $center->id)
            ->where('parity_ok', false)
            ->count();

        // Data freshness: last submission or proctoring log update
        $lastActivity = GeneralExamSubmission::where('center_id', $center->id)
            ->orderBy('updated_at', 'desc')
            ->first();

        return [
            'total_candidates' => $totalCandidates,
            'submitted' => $submitted,
            'in_progress' => $inProgress,
            'completion_rate' => $completionRate,
            'avg_score' => $avgScore ? round($avgScore, 2) : null,
            'flagged_proctoring' => $flaggedProctoring,
            'last_sync_at' => $lastSync?->started_at,
            'backlog' => $backlog,
            'parity_failures' => $parityFailures,
            'last_activity_at' => $lastActivity?->updated_at,
        ];
    }

    /**
     * Show detailed view for a single center.
     */
    public function show(Center $center)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        // Non-admin users can only view their own center
        if (!$isAdmin && $user->center_id !== $center->id) {
            abort(403);
        }

        $stats = $this->getCenterStats($center);

        // Recent submissions for this center
        $recentSubmissions = GeneralExamSubmission::where('center_id', $center->id)
            ->with('exam')
            ->latest()
            ->limit(50)
            ->get();

        // Recent sync runs
        $recentSyncRuns = SyncRun::where('direction', 'push')
            ->latest()
            ->limit(20)
            ->get();

        return view('center-sync.centers.show', compact(
            'center',
            'stats',
            'recentSubmissions',
            'recentSyncRuns',
            'isAdmin'
        ));
    }

    /**
     * Show parity alerts page.
     */
    public function parityAlerts(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        $query = GeneralExamSubmission::where('parity_ok', false)
            ->whereNotNull('parity_checked_at')
            ->with('exam');

        if (!$isAdmin && $user->center_id) {
            $query->where('center_id', $user->center_id);
        }

        $alerts = $query->latest('parity_checked_at')->paginate(50);

        return view('center.parity-alerts', compact('alerts', 'isAdmin'));
    }
}
