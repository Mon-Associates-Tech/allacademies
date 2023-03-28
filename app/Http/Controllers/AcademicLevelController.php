<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicLevelRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;

class AcademicLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicGroup $academicGroup)
    {
        $this->authorize('moderate');

        $academicLevels = $academicGroup->academicLevels()->latest('id')->paginate();

        return view('academic-levels.index', [
            'academicGroup' => $academicGroup,
            'academicLevels' => $academicLevels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicGroup $academicGroup)
    {
        $this->authorize('administrate');

        return view('academic-levels.create', [
            'academicGroup' => $academicGroup,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicGroup $academicGroup, AcademicLevelRequest $request)
    {
        $this->authorize('administrate');

        $academicLevel = $academicGroup->academicLevels()->create($request->validated());

        return to_route('academic-groups.academic-levels.index', ['academic_group' => $academicGroup])
            ->with('success', __('status.resource.created', ['name' => $academicLevel->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicLevel $academicLevel)
    {
        $this->authorize('moderate');

        $academicLevel->load('academicGroup')->loadCount('academicSubjects');

        return view('academic-levels.show', [
            'academicLevel' => $academicLevel,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicLevel $academicLevel)
    {
        $this->authorize('administrate');

        $academicLevel->load('academicGroup');

        return view('academic-levels.edit', [
            'academicLevel' => $academicLevel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function update(AcademicLevelRequest $request, AcademicLevel $academicLevel)
    {
        $this->authorize('administrate');

        $academicLevel->update($request->validated());

        return to_route('academic-levels.show', ['academic_level' =>  $academicLevel])
            ->with('success', __('status.resource.updated', ['name' => $academicLevel->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicLevel $academicLevel)
    {
        $this->authorize('administrate');

        $academicLevel->load('academicGroup')->delete();

        return to_route('academic-groups.academic-levels.index', ['academic_group' => $academicLevel->academicGroup])
            ->with('success', __('status.resource.deleted', ['name' => $academicLevel->name]));
    }
}
