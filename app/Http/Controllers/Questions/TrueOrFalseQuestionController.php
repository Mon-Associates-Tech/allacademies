<?php

namespace App\Http\Controllers\Questions;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrueOrFalseQuestionRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\TrueOrFalseQuestion;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TrueOrFalseQuestionController extends Controller
{
    use HasSubtopic;
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestions = $academicTopic->trueOrFalseQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('questions.true-or-false-questions.index', [
            'trueOrFalseQuestions' => $trueOrFalseQuestions,
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
     * @param TrueOrFalseQuestionRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, TrueOrFalseQuestionRequest $request): RedirectResponse
    {
        $this->authorize('moderate');

        $data = $request->validated();

        $trueOrFalseQuestion = $academicTopic->trueOrFalseQuestions()->create($data);
        $data['academic_subtopic_id'] = $this->getSubtopicId($trueOrFalseQuestion, $request);
        $trueOrFalseQuestion->update([
            'academic_subtopic_id' => $data['academic_subtopic_id'],
        ]);

        return to_route('true-or-false-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.created', ['name' => $trueOrFalseQuestion->question->summary]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('questions.true-or-false-questions.create', [
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
     * @param TrueOrFalseQuestion $trueOrFalseQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic,TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $trueOrFalseQuestion->load('subtopic');

        return view('questions.true-or-false-questions.show', [
            'trueOrFalseQuestion' => $trueOrFalseQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param TrueOrFalseQuestion $trueOrFalseQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $trueOrFalseQuestion->load('subtopic');

        return view('questions.true-or-false-questions.edit', [
            'trueOrFalseQuestion' => $trueOrFalseQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TrueOrFalseQuestionRequest $request
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param TrueOrFalseQuestion $true_or_false_question
     * @return RedirectResponse
     */
    public function update(TrueOrFalseQuestionRequest $request, AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, TrueOrFalseQuestion $true_or_false_question): RedirectResponse
    {
        $this->authorize('moderate');

        $data = $request->validated();
        $data['academic_subtopic_id'] = $this->getSubtopicId($true_or_false_question, $request);
        $true_or_false_question->update($data);

        return to_route('true-or-false-questions.show', ['true_or_false_question' => $true_or_false_question, 'academic_topic' => $true_or_false_question->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.updated', ['name' => $true_or_false_question->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param TrueOrFalseQuestion $trueOrFalseQuestion
     * @return RedirectResponse
     */
    public function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, TrueOrFalseQuestion $trueOrFalseQuestion): RedirectResponse
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic')->delete();

        return to_route('true-or-false-questions.index', ['academic_topic' => $trueOrFalseQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.deleted', ['name' => $trueOrFalseQuestion->question->summary]));
    }
}
