<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionAvailabilityController extends Controller
{
    /**
     * Check question availability for examination generation
     */
    public function check(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'academic_subject_id' => 'required|exists:academic_subjects,id',
            'academic_group_id' => 'nullable|exists:academic_groups,id',
            'academic_level_id' => 'nullable|exists:academic_levels,id',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'exists:academic_topics,id',
            'subtopic_ids' => 'nullable|array',
            'subtopic_ids.*' => 'exists:academic_subtopics,id',
            'question_type' => 'required|in:essay_questions,multiple_choice_questions,true_or_false_questions',
            'required_count' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $subject = AcademicSubject::findOrFail($data['academic_subject_id']);

        // Verify relationships if provided
        if (isset($data['academic_level_id']) && $subject->academic_level_id != $data['academic_level_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Subject does not belong to the specified level',
            ], 400);
        }

        if (isset($data['academic_group_id'])) {
            $level = $subject->academicLevel;
            if ($level && $level->academic_group_id != $data['academic_group_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject does not belong to the specified group',
                ], 400);
            }
        }

        $table = $data['question_type'];
        $requiredCount = $data['required_count'];
        $topicIds = $data['topic_ids'] ?? [];
        $subtopicIds = $data['subtopic_ids'] ?? [];

        // If no topics/subtopics specified, get all topics for the subject
        if (empty($topicIds) && empty($subtopicIds)) {
            $topicIds = AcademicTopic::where('academic_subject_id', $subject->id)
                ->pluck('id')
                ->toArray();
        }

        $availability = $this->checkAvailability($table, $topicIds, $subtopicIds, $requiredCount);

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                ],
                'question_type' => $data['question_type'],
                'required_count' => $requiredCount,
                'available_count' => $availability['available_count'],
                'sufficient' => $availability['sufficient'],
                'breakdown' => $availability['breakdown'],
            ],
        ]);
    }

    /**
     * Check question availability with detailed breakdown
     */
    private function checkAvailability(
        string $table,
        array $topicIds,
        array $subtopicIds,
        int $requiredCount
    ): array {
        $breakdown = [
            'by_topic' => [],
            'by_subtopic' => [],
        ];

        $totalAvailable = 0;

        // Check subtopic availability
        if (!empty($subtopicIds)) {
            foreach ($subtopicIds as $subtopicId) {
                $subtopic = AcademicSubtopic::find($subtopicId);
                if (!$subtopic) {
                    continue;
                }

                $count = DB::table($table)
                    ->where('academic_subtopic_id', $subtopicId)
                    ->count();

                $breakdown['by_subtopic'][] = [
                    'id' => $subtopic->id,
                    'name' => $subtopic->name,
                    'available' => $count,
                ];

                $totalAvailable += $count;
            }
        }

        // Check topic availability
        if (!empty($topicIds)) {
            foreach ($topicIds as $topicId) {
                $topic = AcademicTopic::find($topicId);
                if (!$topic) {
                    continue;
                }

                // Count all questions for this topic
                $count = DB::table($table)
                    ->where('academic_topic_id', $topicId)
                    ->count();

                // If we're checking specific subtopics, don't double count
                if (empty($subtopicIds)) {
                    $totalAvailable += $count;
                }

                $breakdown['by_topic'][] = [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'available' => $count,
                ];
            }
        }

        return [
            'available_count' => $totalAvailable,
            'sufficient' => $totalAvailable >= $requiredCount,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Get comprehensive question statistics for a subject
     */
    public function statistics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'academic_subject_id' => 'required|exists:academic_subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subjectId = $request->input('academic_subject_id');
        $subject = AcademicSubject::findOrFail($subjectId);

        $topics = AcademicTopic::where('academic_subject_id', $subjectId)
            ->with('subtopics')
            ->get()
            ->map(function ($topic) {
                $essayCount = DB::table('essay_questions')
                    ->where('academic_topic_id', $topic->id)
                    ->count();
                $mcqCount = DB::table('multiple_choice_questions')
                    ->where('academic_topic_id', $topic->id)
                    ->count();
                $tofCount = DB::table('true_or_false_questions')
                    ->where('academic_topic_id', $topic->id)
                    ->count();

                $subtopics = $topic->subtopics->map(function ($subtopic) {
                    return [
                        'id' => $subtopic->id,
                        'name' => $subtopic->name,
                        'essay_questions' => DB::table('essay_questions')
                            ->where('academic_subtopic_id', $subtopic->id)
                            ->count(),
                        'multiple_choice_questions' => DB::table('multiple_choice_questions')
                            ->where('academic_subtopic_id', $subtopic->id)
                            ->count(),
                        'true_or_false_questions' => DB::table('true_or_false_questions')
                            ->where('academic_subtopic_id', $subtopic->id)
                            ->count(),
                    ];
                });

                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'essay_questions' => $essayCount,
                    'multiple_choice_questions' => $mcqCount,
                    'true_or_false_questions' => $tofCount,
                    'total_questions' => $essayCount + $mcqCount + $tofCount,
                    'subtopics' => $subtopics,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                ],
                'topics' => $topics,
            ],
        ]);
    }
}
