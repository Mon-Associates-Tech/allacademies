<?php

namespace App\Http\Controllers;

use App\Http\Requests\MultipleChoiceQuestionRequest;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Http\Request;

class MultipleChoiceQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicTopic $academicTopic)
    {
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
        $academicTopic->multipleChoiceQuestion()->create($request->validated());

        return redirect()->route('academic-topics.multiple-choice-questions.create', ['academic_topic' => $academicTopic]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        //
    }
}
