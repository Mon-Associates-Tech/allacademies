<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function index()
    {
        $this->authorize('moderate');

        $multipleChoiceQuestions = MultipleChoiceQuestion::query()->with('academicTopic.academicSubject.academicLevel')->get();

        return view('multiple-choice-questions.index', [
            'multipleChoiceQuestions' => $multipleChoiceQuestions,
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

        $academicTopic->multipleChoiceQuestions()->create($request->validated());

        return to_route('academic-topics.multiple-choice-questions.create', ['academic_topic' => $academicTopic]);
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->authorize('moderate');
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
    }
}
