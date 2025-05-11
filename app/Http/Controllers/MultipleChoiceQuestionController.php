<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use App\Http\Requests\MultipleChoiceQuestionRequest;

class MultipleChoiceQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicTopic $academicTopic, MultipleChoiceQuestionRequest $request)
    {
        $this->authorize('moderate');

        $subTopic = AcademicSubtopic::create(['name' => $request->subtopic, 'academic_topic_id' => $academicTopic->id]);
        $data = $request->validated();
        $data['academic_subtopic_id'] = $subTopic->id;
        $multipleChoiceQuestion = $academicTopic->multipleChoiceQuestions()->create($data);




        return to_route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $academicTopic])
            ->with('success', __('status.resource.created', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
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
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(MultipleChoiceQuestionRequest $request, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->update($request->validated());

        return to_route('multiple-choice-questions.show', ['multiple_choice_question' =>  $multipleChoiceQuestion])
            ->with('success', __('status.resource.updated', ['name' => $multipleChoiceQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');

        $multipleChoiceQuestion->load('academicTopic')->delete();

        return to_route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic])
            ->with('success', __('status.resource.deleted', ['name' => $multipleChoiceQuestion->question->summary]));
    }
}
