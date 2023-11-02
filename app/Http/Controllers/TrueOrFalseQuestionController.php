<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicTopic;
use App\Models\TrueOrFalseQuestion;
use App\Http\Requests\TrueOrFalseQuestionRequest;

class TrueOrFalseQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestions = $academicTopic->trueOrFalseQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('true-or-false-questions.index', [
            'trueOrFalseQuestions' => $trueOrFalseQuestions,
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

        return view('true-or-false-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicTopic $academicTopic, TrueOrFalseQuestionRequest $request)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion = $academicTopic->trueOrFalseQuestions()->create($request->validated());

        return to_route('academic-topics.true-or-false-questions.index', ['academic_topic' => $academicTopic])
            ->with('success', __('status.resource.created', ['name' => $trueOrFalseQuestion->question->summary]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('true-or-false-questions.show', [
            'trueOrFalseQuestion' => $trueOrFalseQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('true-or-false-questions.edit', [
            'trueOrFalseQuestion' => $trueOrFalseQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(TrueOrFalseQuestionRequest $request, TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->update($request->validated());

        return to_route('true-or-false-questions.show', ['true_or_false_question' =>  $trueOrFalseQuestion])
            ->with('success', __('status.resource.updated', ['name' => $trueOrFalseQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic')->delete();

        return to_route('academic-topics.true-or-false-questions.index', ['academic_topic' => $trueOrFalseQuestion->academicTopic])
            ->with('success', __('status.resource.deleted', ['name' => $trueOrFalseQuestion->question->summary]));
    }
}
