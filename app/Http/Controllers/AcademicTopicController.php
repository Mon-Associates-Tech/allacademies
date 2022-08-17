<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicTopicRequest;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use Illuminate\Http\Request;

class AcademicTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('academic-topics.index', [
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $academicLevels = AcademicLevel::all(['id', 'name', 'label'])->toArray();
        $academicSubjects = AcademicSubject::all(['id', 'name', 'code'])->toArray();

        return view('academic-topics.create', [
            'academicLevels' => $academicLevels,
            'academicSubjects' => $academicSubjects
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicTopicRequest $request)
    {
        AcademicTopic::query()->create($request->validated());

        return redirect()->route('academic-topics.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicTopic  $academicTopic
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicTopic $academicTopic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicTopic  $academicTopic
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicTopic $academicTopic)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicTopic  $academicTopic
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicTopic $academicTopic)
    {
        //
    }
}
