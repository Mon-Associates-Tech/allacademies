<?php

namespace App\Http\Controllers;

use App\Http\Requests\TheoryQuestionRequest;
use Illuminate\Http\Request;
use App\Models\AcademicTopic;
use App\Models\TheoryQuestion;

class TheoryQuestionController extends Controller
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
        return view('theory-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicTopic $academicTopic, TheoryQuestionRequest $request)
    {
        $academicTopic->theoryQuestion()->create($request->validated());

        return redirect()->route('academic-topics.theory-questions.create', ['academic_topic' => $academicTopic]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TheoryQuestion  $theoryQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(TheoryQuestion $theoryQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TheoryQuestion  $theoryQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(TheoryQuestion $theoryQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TheoryQuestion  $theoryQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TheoryQuestion $theoryQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TheoryQuestion  $theoryQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(TheoryQuestion $theoryQuestion)
    {
        //
    }
}
