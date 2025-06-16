<?php

namespace App\Http\Controllers\Questions;

use App\Http\Controllers\Controller;
use App\Http\Requests\EssayQuestionRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EssayQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestions = $academicTopic->essayQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();
        $essayQuestions->load('subtopic');

        return view('questions.essay-questions.index', [
            'essayQuestions' => $essayQuestions,
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
     * @param EssayQuestionRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, EssayQuestionRequest $request): RedirectResponse
    {
        $this->authorize('moderate');
        $data = $request->validated();

        if (isset($request->subtopic)) {
            $subTopic = AcademicSubtopic::firstOrCreate(
                ['name' => $request->subtopic],
                ['name' => $request->subtopic, 'academic_topic_id' => $academicTopic->id]
            );
            $data['academic_subtopic_id'] = $subTopic->id;
        }

        $essayQuestion = $academicTopic->essayQuestions()->create($data);

        return to_route('essay-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.created', ['name' => $essayQuestion->question->summary]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        return view('questions.essay-questions.create', [
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
     * @param EssayQuestion $essayQuestion
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public
    function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, EssayQuestion $essayQuestion)
    {
//        $this->authorize('moderate');

        $essayQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $essayQuestion->load('subtopic');

        return view('questions.essay-questions.show', [
            'essayQuestion' => $essayQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param EssayQuestion $essayQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public
    function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $essayQuestion->load('subtopic');

        return view('questions.essay-questions.edit', [
            'essayQuestion' => $essayQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EssayQuestionRequest $request
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param EssayQuestion $essayQuestion
     * @return RedirectResponse
     */
    public
    function update(EssayQuestionRequest $request, AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, EssayQuestion $essayQuestion): RedirectResponse
    {
        $this->authorize('moderate');

        $data = $request->validated();
        if (isset($request->subtopic, $essayQuestion->subtopic->name)) {
            $subTopic = AcademicSubtopic::updateOrCreate(
                ['name' => $essayQuestion->subtopic->name],
                ['name' => $request->subtopic, 'academic_topic_id' => $essayQuestion->academic_topic_id]
            );
            $data['academic_subtopic_id'] = $subTopic->id;
        }

        $essayQuestion->update($data);

        return to_route('essay-questions.show', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.updated', ['name' => $essayQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param EssayQuestion $essayQuestion
     * @return RedirectResponse
     */
    public
    function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, EssayQuestion $essayQuestion): RedirectResponse
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic')->delete();

        return to_route('essay-questions.index', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.deleted', ['name' => $essayQuestion->question->summary]));
    }
}
