<?php

namespace App\Http\Controllers\Questions;

use App\Http\Controllers\Controller;
use App\Http\Requests\MultipleChoiceQuestionRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MultipleChoiceQuestionController extends Controller
{
    use HasSubtopic;
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param MultipleChoiceQuestionRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject,  AcademicTopic $academicTopic, MultipleChoiceQuestionRequest $request): RedirectResponse
    {
        $this->authorize('moderate');

        $data = $request->validated();

        $multipleChoiceQuestion = $academicTopic->multipleChoiceQuestions()->create($data);
        $data['academic_subtopic_id'] = $this->getSubtopicId($multipleChoiceQuestion, $request);
        $multipleChoiceQuestion->update($data);


        return to_route('multiple-choice-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => $academicSubject , 'academic_level' => $academicLevel, 'academic_group' => $academicGroup])
            ->with('success', __('status.resource.created', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject,  AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup', 'subtopics');

        return view('questions.multiple-choice-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('questions.multiple-choice-questions.show', [
            'multipleChoiceQuestion' => $multipleChoiceQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $multipleChoiceQuestion->load('subtopic');

        return view('questions.multiple-choice-questions.edit', [
            'multipleChoiceQuestion' => $multipleChoiceQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param MultipleChoiceQuestionRequest $request
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return RedirectResponse
     */
    public function update(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestionRequest $request, MultipleChoiceQuestion $multipleChoiceQuestion): RedirectResponse
    {
        $this->authorize('moderate');

        $data = $request->validated();

        $data['academic_subtopic_id'] = $this->getSubtopicId($multipleChoiceQuestion, $request);

        $multipleChoiceQuestion->update($data);

        return to_route('multiple-choice-questions.show', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.updated', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return RedirectResponse
     */
    public function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, MultipleChoiceQuestion $multipleChoiceQuestion): RedirectResponse
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->load('academicTopic')->delete();

        return to_route('multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.deleted', ['name' => $multipleChoiceQuestion->question->summary]));
    }
}
