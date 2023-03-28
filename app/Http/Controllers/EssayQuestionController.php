<?php

namespace App\Http\Controllers;

use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Http\Requests\EssayQuestionRequest;

class EssayQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $essayQuestions = $academicTopic->essayQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();

        return view('essay-questions.index', [
            'essayQuestions' => $essayQuestions,
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

        return view('essay-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicTopic $academicTopic, EssayQuestionRequest $request)
    {
        $this->authorize('moderate');

        $essayQuestion = $academicTopic->essayQuestions()->create($request->validated());

        return to_route('academic-topics.essay-questions.index', ['academic_topic' => $academicTopic])
            ->with('success', __('status.resource.created', ['name' => $essayQuestion->question->summary]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('essay-questions.show', [
            'essayQuestion' => $essayQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('essay-questions.edit', [
            'essayQuestion' => $essayQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(EssayQuestionRequest $request, EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->update($request->validated());

        return to_route('essay-questions.show', ['essay_question' =>  $essayQuestion])
            ->with('success', __('status.resource.updated', ['name' => $essayQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic')->delete();

        return to_route('academic-topics.essay-questions.index', ['academic_topic' => $essayQuestion->academicTopic])
            ->with('success', __('status.resource.deleted', ['name' => $essayQuestion->question->summary]));
    }
}
