<?php

namespace App\Http\Controllers\Questions;

use App\Http\Controllers\Controller;
use App\Http\Requests\MultipleChoiceQuestionRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MultipleChoiceQuestionController extends Controller
{
    use HasSubtopic;

    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestions = $academicTopic->multipleChoiceQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();
        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('questions.multiple-choice-questions.index', [
            'multipleChoiceQuestions' => $multipleChoiceQuestions,
            'academicTopic' => $academicTopic,
        ]);
    }

    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestionRequest $request): RedirectResponse
    {
        $this->authorize('moderate');

        $data = $request->validated();
        $multipleChoiceQuestion = $academicTopic->multipleChoiceQuestions()->create($data);

        $data['academic_subtopic_id'] = $this->getSubtopicId($multipleChoiceQuestion, $request);
        $multipleChoiceQuestion->update($data);

        return to_route('multiple-choice-questions.index', [
            'academic_topic' => $academicTopic,
            'academic_subject' => $academicSubject,
            'academic_level' => $academicLevel,
            'academic_group' => $academicGroup
        ])->with('success', __('status.resource.created', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');
        $academicTopic->load('academicSubject.academicLevel.academicGroup', 'subtopics');

        return view('questions.multiple-choice-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');
        $multipleChoiceQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('questions.multiple-choice-questions.show', [
            'multipleChoiceQuestion' => $multipleChoiceQuestion,
        ]);
    }

    public function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');
        $multipleChoiceQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup', 'subtopic');

        return view('questions.multiple-choice-questions.edit', [
            'multipleChoiceQuestion' => $multipleChoiceQuestion,
        ]);
    }

    public function update(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestionRequest $request, MultipleChoiceQuestion $multipleChoiceQuestion): RedirectResponse
    {
        $this->authorize('moderate');

        $data = $request->validated();
        $data['academic_subtopic_id'] = $this->getSubtopicId($multipleChoiceQuestion, $request);
        $multipleChoiceQuestion->update($data);

        return to_route('multiple-choice-questions.show', [
            'multiple_choice_question' => $multipleChoiceQuestion,
            'academic_subject' => getRouteParameter('academic_subject'),
            'academic_topic' => getRouteParameter('academic_topic'),
            'academic_level' => getRouteParameter('academic_level'),
            'academic_group' => getRouteParameter('academic_group')
        ])->with('success', __('status.resource.updated', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    public function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestion $multipleChoiceQuestion): RedirectResponse
    {
        $this->authorize('moderate');
        $multipleChoiceQuestion->load('academicTopic')->delete();

        return to_route('multiple-choice-questions.index', [
            'academic_topic' => $multipleChoiceQuestion->academicTopic,
            'academic_subject' => getRouteParameter('academic_subject'),
            'academic_level' => getRouteParameter('academic_level'),
            'academic_group' => getRouteParameter('academic_group')
        ])->with('success', __('status.resource.deleted', ['name' => $multipleChoiceQuestion->question->summary]));
    }

       public function bulkEdit(Request $request, AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic): View
    {
        $this->authorize('moderate');
        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        $search = $request->query('search');
        $query = $academicTopic->multipleChoiceQuestions()->latest('created_at');

        if ($search) {
            $query->whereRaw('LOWER(question) LIKE ?', ['%'.strtolower($search).'%']);
        }

        // ✅ FIX: Use paginate(20) instead of get() to return a Paginator instance
        $questions = $query->paginate(5)->withQueryString();

        return view('questions.multiple-choice-questions.bulk-edit', [
            'academicTopic'   => $academicTopic,
            'academicSubject' => $academicSubject,
            'academicLevel'   => $academicLevel,
            'academicGroup'   => $academicGroup,
            'questions'       => $questions, // This is now a LengthAwarePaginator
            'search'          => $search,
        ]);
    }

    public function bulkUpdate(Request $request, AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic): RedirectResponse
    {
        $this->authorize('moderate');

        $rows = $request->input('questions', []);
        $updatedCount = 0;

        foreach ($rows as $index => $row) {
            $id = $row['id'] ?? null;
            if (! $id) continue;

            $question = MultipleChoiceQuestion::where('academic_topic_id', $academicTopic->id)->find($id);
            if (! $question) continue;

            $updateData = [];

            // Use array_key_exists to distinguish between "field not submitted (truncated by PHP)"
            // and "field intentionally submitted as empty string by the user".
            if (array_key_exists('question', $row)) {
                $updateData['question'] = $row['question'];
            }
            if (array_key_exists('option_a', $row)) {
                $updateData['option_a'] = $row['option_a'];
            }
            if (array_key_exists('option_b', $row)) {
                $updateData['option_b'] = $row['option_b'];
            }
            if (array_key_exists('option_c', $row)) {
                $updateData['option_c'] = $row['option_c'];
            }
            if (array_key_exists('option_d', $row)) {
                $updateData['option_d'] = $row['option_d'];
            }
            if (array_key_exists('option_e', $row)) {
                $updateData['option_e'] = $row['option_e'];
            }
            if (array_key_exists('answer', $row)) {
                $updateData['answer'] = strtolower((string) $row['answer']);
            }
            if (array_key_exists('difficulty_level', $row)) {
                $updateData['difficulty_level'] = $row['difficulty_level'];
            }
            if (array_key_exists('score', $row)) {
                $updateData['score'] = $row['score'];
            }

            if (!empty($updateData)) {
                $question->update($updateData);
                $updatedCount++;
            }
        }

        return to_route('multiple-choice-questions.index', [
            'academic_topic'   => $academicTopic,
            'academic_subject' => $academicSubject,
            'academic_level'   => $academicLevel,
            'academic_group'   => $academicGroup,
        ])->with('success', "Successfully updated {$updatedCount} question(s).");
    }
}
