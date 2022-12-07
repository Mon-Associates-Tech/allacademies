<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicTopic;
use App\Models\AcademicSubject;
use App\Http\Requests\AcademicTopicRequest;

class AcademicTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('moderate');

        $academicTopics = AcademicTopic::query()->with('academicSubject.academicLevel')->get();

        return view('academic-topics.index', [
            'academicTopics' => $academicTopics
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');

        return view('academic-topics.create', [
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicSubject $academicSubject, AcademicTopicRequest $request)
    {
        $this->authorize('administrate');

        $academicSubject->academicTopics()->create($request->validated());

        return to_route('academic-topics.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicTopic  $academicTopic
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicTopic  $academicTopic
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicTopic  $academicTopic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicTopic  $academicTopic
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');
    }
}
