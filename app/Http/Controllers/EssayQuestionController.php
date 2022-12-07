<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function index()
    {
        $this->authorize('moderate');

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

        $academicTopic->essayQuestions()->create($request->validated());

        return to_route('academic-topics.essay-questions.create', ['academic_topic' => $academicTopic]);
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
        $this->authorize('moderate');
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
    }
}
