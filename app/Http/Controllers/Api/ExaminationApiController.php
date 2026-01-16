<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Services\QuestionGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExaminationApiController extends Controller
{
    /**
     * Get all academic groups.
     */
    public function getGroups(): JsonResponse
    {
        $groups = AcademicGroup::active()->select('id', 'name')->get();

        return response()->json(['data' => $groups]);
    }

    /**
     * Get academic levels for a specific group.
     */
    public function getLevels(AcademicGroup $group): JsonResponse
    {
        $levels = $group->academicLevels()->select('id', 'name', 'label')->get();

        return response()->json(['data' => $levels]);
    }

    /**
     * Get subjects for a specific level.
     */
    public function getSubjects(AcademicLevel $level): JsonResponse
    {
        $subjects = $level->academicSubjects()->select('id', 'name', 'code')->get();

        return response()->json(['data' => $subjects]);
    }

    /**
     * Get topics and subtopics for a subject with question counts.
     */
    public function getTopics(AcademicSubject $subject): JsonResponse
    {
        $topics = AcademicTopic::where('academic_subject_id', $subject->id)
            ->select(['id', 'name'])
            ->withCount([
                'essayQuestions',
                'multipleChoiceQuestions',
                'trueOrFalseQuestions',
            ])
            ->with([
                'subtopics' => function ($query) {
                    $query->select('id', 'name', 'academic_topic_id')
                        ->withCount([
                            'essayQuestions',
                            'multipleChoiceQuestions',
                            'trueOrFalseQuestions',
                        ]);
                },
            ])
            ->get();

        return response()->json(['data' => $topics]);
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'heading' => 'required|array',
            'sections' => 'required|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $preprocessedSections = QuestionGenerator::preprocessSections($request->input('sections'));

            // 1. Generate the structure with IDs
            $generatedData = QuestionGenerator::generate(
                $request->input('heading'),
                $preprocessedSections,
                $request->input('metadata', [])
            );

            // 2. Hydrate the sections with full question objects
            $questionGenerator = new QuestionGenerator;

            $generatedData['sections'] = collect($generatedData['sections'])->map(function ($section) use ($questionGenerator) {
                if (! empty($section['questions'])) {
                    // The generator returns IDs in the 'questions' key.
                    // We need to determine the type string expected by fetchCompleteQuestions
                    // e.g., convert 'multiple_choice_questions' to 'multiple_choice'
                    $questionType = str_replace('_questions', '', $section['type']);

                    // Fetch full objects using the IDs
                    $fullQuestions = $questionGenerator->fetchCompleteQuestions($section['questions'], $questionType);

                    // Format them (handling complex objects like 'Mark' or JSON strings)
                    $section['questions'] = $questionGenerator->formatExistingQuestions($fullQuestions);
                }

                return $section;
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $generatedData,
            ]);

        } catch (\App\Exceptions\NotEnoughQuestionsException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough questions available.',
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate questions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
