<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrueOrFalseRequest;
use Illuminate\Http\Request;
use App\Models\AcademicTopic;
use App\Models\TrueOrFalseQuestion;

class TrueOrFalseQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trueOrFalseQuestions = TrueOrFalseQuestion::query()->with('academicTopic.academicSubject.academicLevel')->get();

        return view('true-or-false-questions.index', [
            'trueOrFalseQuestions' => $trueOrFalseQuestions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicTopic $academicTopic)
    {
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
    public function store(AcademicTopic $academicTopic, TrueOrFalseRequest $request)
    {
        $academicTopic->trueOrFalseQuestions()->create($request->validated());

        return to_route('academic-topics.true-or-false-questions.create', ['academic_topic' => $academicTopic]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TrueOrFalseQuestion  $trueOrFalseQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        //
    }
}
