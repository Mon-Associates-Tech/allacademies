<?php

namespace App\Http\Controllers;

use App\Http\Requests\EssayQuestionRequest;
use Illuminate\Http\Request;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;

class EssayQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $essayQuestions = EssayQuestion::query()->with('academicTopic.academicSubject.academicLevel')->get();

        return view('essay-questions.index', [
            'essayQuestions' => $essayQuestions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicTopic $academicTopic)
    {
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
        $academicTopic->essayQuestion()->create($request->validated());

        return redirect()->route('academic-topics.essay-questions.create', ['academic_topic' => $academicTopic]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(EssayQuestion $essayQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(EssayQuestion $essayQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EssayQuestion $essayQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(EssayQuestion $essayQuestion)
    {
        //
    }
}
