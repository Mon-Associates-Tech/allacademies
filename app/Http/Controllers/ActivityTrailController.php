<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;

class ActivityTrailController extends Controller
{
    /**
     * Display the activity trail page
     */
    public function index()
    {
        return view('activity-trail');
    }

    /**
     * Export activity data as CSV
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'question_type' => 'nullable|in:essay,multiple_choice,true_or_false',
            'action' => 'nullable|in:question_created,question_updated,question_deleted',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Activity::with(['causer', 'subject'])
            ->when($request->user_id, fn($q) => $q->where('causer_id', $request->user_id))
            ->when($request->question_type, fn($q) => $q->whereJsonContains('properties->question_type', $request->question_type))
            ->when($request->action, fn($q) => $q->where('description', $request->action))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->whereIn('description', ['question_created', 'question_updated', 'question_deleted'])
            ->orderBy('created_at', 'desc');

        $activities = $query->get();

        $csvData = [];
        $csvData[] = [
            'Date',
            'Time',
            'User Name',
            'User Email',
            'Action',
            'Question Type',
            'Difficulty Level',
            'Score',
            'Changes',
            'Subject ID',
            'Subject Type'
        ];

        foreach ($activities as $activity) {
            $changes = '';
            if ($activity->description === 'question_updated' && isset($activity->properties['changes'])) {
                $changes = implode(', ', array_keys($activity->properties['changes']));
            }

            $csvData[] = [
                $activity->created_at->format('Y-m-d'),
                $activity->created_at->format('H:i:s'),
                $activity->causer->name ?? 'Unknown',
                $activity->causer->email ?? '',
                str_replace('question_', '', $activity->description),
                $activity->properties['question_type'] ?? '',
                $activity->properties['difficulty_level'] ?? '',
                $activity->properties['score'] ?? '',
                $changes,
                $activity->subject_id ?? '',
                $activity->subject_type ?? '',
            ];
        }

        $filename = 'activity-trail-' . Carbon::now()->format('Y-m-d-H-i-s') . '.csv';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $handle = fopen($tempFile, 'w');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'download_url' => route('activity-trail.download', ['file' => basename($tempFile)]),
            'filename' => $filename
        ]);
    }

    /**
     * Download exported CSV file
     */
    public function download(Request $request, string $file)
    {
        $filePath = sys_get_temp_dir() . '/' . $file;

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, 'activity-trail-export.csv')->deleteFileAfterSend();
    }

    /**
     * Get activity statistics for dashboard
     */
    public function stats(Request $request): JsonResponse
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(30);
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $baseQuery = Activity::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('description', ['question_created', 'question_updated', 'question_deleted']);

        $stats = [
            'total_activities' => (clone $baseQuery)->count(),
            'created' => (clone $baseQuery)->where('description', 'question_created')->count(),
            'updated' => (clone $baseQuery)->where('description', 'question_updated')->count(),
            'deleted' => (clone $baseQuery)->where('description', 'question_deleted')->count(),
            'unique_users' => (clone $baseQuery)->distinct('causer_id')->count('causer_id'),
        ];

        // Activity by question type
        $questionTypeStats = (clone $baseQuery)
            ->selectRaw('JSON_EXTRACT(properties, "$.question_type") as question_type, COUNT(*) as count')
            ->groupBy('question_type')
            ->get()
            ->pluck('count', 'question_type')
            ->toArray();

        // Daily activity trend (last 30 days)
        $dailyTrend = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        return response()->json([
            'stats' => $stats,
            'question_type_distribution' => $questionTypeStats,
            'daily_trend' => $dailyTrend,
        ]);
    }

    /**
     * Get activity details
     */
    public function show(Activity $activity): JsonResponse
    {
        $activity->load(['causer', 'subject']);

        return response()->json([
            'activity' => $activity,
            'formatted_properties' => $this->formatActivityProperties($activity),
        ]);
    }

    /**
     * Format activity properties for display
     */
    private function formatActivityProperties(Activity $activity): array
    {
        $formatted = [];
        $properties = $activity->properties ?? [];

        if (isset($properties['question_type'])) {
            $formatted['Question Type'] = match($properties['question_type']) {
                'essay' => 'Essay Question',
                'multiple_choice' => 'Multiple Choice Question',
                'true_or_false' => 'True or False Question',
                default => ucwords(str_replace('_', ' ', $properties['question_type']))
            };
        }

        if (isset($properties['difficulty_level'])) {
            $formatted['Difficulty Level'] = $properties['difficulty_level'];
        }

        if (isset($properties['score'])) {
            $formatted['Score'] = $properties['score'];
        }

        if (isset($properties['academic_topic_id'])) {
            $formatted['Academic Topic ID'] = $properties['academic_topic_id'];
        }

        if (isset($properties['academic_subtopic_id'])) {
            $formatted['Academic Subtopic ID'] = $properties['academic_subtopic_id'];
        }

        if (isset($properties['changes']) && is_array($properties['changes'])) {
            $formatted['Changes'] = [];
            foreach ($properties['changes'] as $field => $change) {
                $formatted['Changes'][$field] = [
                    'from' => $change['old'] ?? 'N/A',
                    'to' => $change['new'] ?? 'N/A',
                ];
            }
        }

        return $formatted;
    }
}
