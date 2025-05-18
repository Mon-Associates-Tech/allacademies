<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use App\Http\Requests\MultipleChoiceQuestionRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MultipleChoiceQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function index(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestions = $academicTopic->multipleChoiceQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('multiple-choice-questions.index', [
            'multipleChoiceQuestions' => $multipleChoiceQuestions,
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function create(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('multiple-choice-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicTopic $academicTopic
     * @param MultipleChoiceQuestionRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicTopic $academicTopic, MultipleChoiceQuestionRequest $request)
    {
        $this->authorize('moderate');

        $data = $request->validated();

        if(isset($request->subtopic)){
            $subTopic = AcademicSubtopic::firstOrCreate(
                ['name' => $request->subtopic],
                ['name' => $request->subtopic, 'academic_topic_id' => $academicTopic->id]
            );
            $data['academic_subtopic_id'] = $subTopic->id;
        }

        $multipleChoiceQuestion = $academicTopic->multipleChoiceQuestions()->create($data);

        return to_route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $academicTopic])
            ->with('success', __('status.resource.created', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    /**
     * Display the specified resource.
     *
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('multiple-choice-questions.show', [
            'multipleChoiceQuestion' => $multipleChoiceQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function edit(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $multipleChoiceQuestion->load('subtopic');

        return view('multiple-choice-questions.edit', [
            'multipleChoiceQuestion' => $multipleChoiceQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MultipleChoiceQuestionRequest $request
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return RedirectResponse
     */
    public function update(MultipleChoiceQuestionRequest $request, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $subTopic = AcademicSubtopic::updateOrCreate(
            ['name' => $multipleChoiceQuestion->subtopic->name],
            ['name' => $request->subtopic, 'academic_topic_id' => $multipleChoiceQuestion->academic_topic_id]
        );
        $data = $request->validated();
        $data['academic_subtopic_id'] = $subTopic->id;

        $multipleChoiceQuestion->update($data);

        return to_route('multiple-choice-questions.show', ['multiple_choice_question' =>  $multipleChoiceQuestion])
            ->with('success', __('status.resource.updated', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param MultipleChoiceQuestion $multipleChoiceQuestion
     * @return Response
     */
    public function destroy(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->load('academicTopic')->delete();

        return to_route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic])
            ->with('success', __('status.resource.deleted', ['name' => $multipleChoiceQuestion->question->summary]));
    }
}
