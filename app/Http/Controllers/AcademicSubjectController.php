<?php

namespace App\Http\Controllers;

use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Http\Requests\AcademicSubjectRequest;

class AcademicSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicLevel $academicLevel)
    {
        $this->authorize('moderate');

        $academicSubjects = $academicLevel->academicSubjects()->latest('id')->paginate();

        $academicLevel->load('academicGroup');

        return view('academic-subjects.index', [
            'academicSubjects' => $academicSubjects,
            'academicLevel' => $academicLevel,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicLevel $academicLevel)
    {
        $this->authorize('administrate');

        $academicLevel->load('academicGroup');

        return view('academic-subjects.create', [
            'academicLevel' => $academicLevel,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicLevel $academicLevel, AcademicSubjectRequest $request)
    {
        $this->authorize('administrate');

        $academicSubject = $academicLevel->academicSubjects()->create($request->validated());

        return to_route('academic-levels.academic-subjects.index', ['academic_level' => $academicLevel])
            ->with('success', __('status.resource.created', ['name' => $academicSubject->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicSubject $academicSubject)
    {
        $this->authorize('moderate');

        $academicSubject->load('academicLevel.academicGroup')->loadCount('academicTopics');

        return view('academic-subjects.show', [
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');

        $academicSubject->load('academicLevel.academicGroup');

        return view('academic-subjects.edit', [
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function update(AcademicSubjectRequest $request, AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');

        $academicSubject->update($request->validated());

        return to_route('academic-subjects.show', ['academic_subject' =>  $academicSubject])
            ->with('success', __('status.resource.updated', ['name' => $academicSubject->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');

        $academicSubject->load('academicLevel')->delete();

        return to_route('academic-levels.academic-subjects.index', ['academic_level' => $academicSubject->academicLevel])
            ->with('success', __('status.resource.deleted', ['name' => $academicSubject->name]));
    }
}
