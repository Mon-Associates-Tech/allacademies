<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display all academic activities
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 50);
        $activities = ActivityLogHelper::getAllAcademicActivities($limit);
        $formattedActivities = ActivityLogHelper::formatActivities($activities);

        return response()->json([
            'success' => true,
            'data' => $formattedActivities,
            'count' => count($formattedActivities),
        ]);
    }

    /**
     * Get activities for a specific model type
     */
    public function byModel(Request $request, string $modelType): JsonResponse
    {
        $validModels = [
            'academicgroup', 'academiclevel', 'academicsubject',
            'academictopic', 'academicsubtopic',
        ];

        if (! in_array(strtolower($modelType), $validModels)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid model type',
                'valid_models' => $validModels,
            ], 400);
        }

        $limit = $request->get('limit', 50);
        $activities = ActivityLogHelper::getActivitiesForModel($modelType, $limit);
        $formattedActivities = ActivityLogHelper::formatActivities($activities);

        return response()->json([
            'success' => true,
            'data' => $formattedActivities,
            'count' => count($formattedActivities),
            'model_type' => ucfirst($modelType),
        ]);
    }

    /**
     * Get activities by user
     */
    public function byUser(Request $request, int $userId): JsonResponse
    {
        $limit = $request->get('limit', 50);
        $activities = ActivityLogHelper::getActivitiesByUser($userId, $limit);
        $formattedActivities = ActivityLogHelper::formatActivities($activities);

        return response()->json([
            'success' => true,
            'data' => $formattedActivities,
            'count' => count($formattedActivities),
            'user_id' => $userId,
        ]);
    }

    /**
     * Get activities within date range
     */
    public function byDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'limit' => 'integer|min:1|max:1000',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $limit = $request->get('limit', 100);

        $activities = ActivityLogHelper::getActivitiesByDateRange($startDate, $endDate, $limit);
        $formattedActivities = ActivityLogHelper::formatActivities($activities);

        return response()->json([
            'success' => true,
            'data' => $formattedActivities,
            'count' => count($formattedActivities),
            'date_range' => [
                'start' => $startDate->format('Y-m-d H:i:s'),
                'end' => $endDate->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Get activity statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $statistics = ActivityLogHelper::getActivityStatistics($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'date_range' => [
                'start' => $startDate?->format('Y-m-d'),
                'end' => $endDate?->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Search activities
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:500',
        ]);

        $keyword = $request->keyword;
        $limit = $request->get('limit', 50);

        $activities = ActivityLogHelper::searchActivities($keyword, $limit);
        $formattedActivities = ActivityLogHelper::formatActivities($activities);

        return response()->json([
            'success' => true,
            'data' => $formattedActivities,
            'count' => count($formattedActivities),
            'keyword' => $keyword,
        ]);
    }

    /**
     * Get activities for a specific model instance
     */
    public function modelInstance(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $modelClasses = [
            'academicgroup' => \App\Models\AcademicGroup::class,
            'academiclevel' => \App\Models\AcademicLevel::class,
            'academicsubject' => \App\Models\AcademicSubject::class,
            'academictopic' => \App\Models\AcademicTopic::class,
            'academicsubtopic' => \App\Models\AcademicSubtopic::class,
        ];

        $modelType = strtolower($modelType);

        if (! isset($modelClasses[$modelType])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid model type',
                'valid_models' => array_keys($modelClasses),
            ], 400);
        }

        $modelClass = $modelClasses[$modelType];
        $model = $modelClass::find($modelId);

        if (! $model) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($modelType).' not found',
            ], 404);
        }

        $limit = $request->get('limit', 50);
        $activities = ActivityLogHelper::getModelActivity($model, $limit);

        return response()->json([
            'success' => true,
            'data' => $activities,
            'count' => count($activities),
            'model' => [
                'type' => ucfirst($modelType),
                'id' => $modelId,
                'identifier' => $model->name ?? $model->label ?? $model->code ?? "ID: {$modelId}",
            ],
        ]);
    }
}
