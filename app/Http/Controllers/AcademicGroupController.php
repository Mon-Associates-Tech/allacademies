<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicGroupRequest;
use App\Models\AcademicGroup;

class AcademicGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('moderate');

        $academicGroups = AcademicGroup::query()->latest('id')->paginate();

        return view('academic-groups.index', [
            'academicGroups' => $academicGroups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('administrate');

        return view('academic-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicGroupRequest $request)
    {
        $this->authorize('administrate');

        $academicGroup = AcademicGroup::query()->create($request->validated());

        return to_route('academic-groups.index')
            ->with('success', __('status.resource.created', ['name' => $academicGroup->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicGroup $academicGroup)
    {
        $this->authorize('moderate');

        $academicGroup->loadCount('academicLevels');

        return view('academic-groups.show', [
            'academicGroup' => $academicGroup,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicGroup $academicGroup)
    {
        $this->authorize('administrate');

        return view('academic-groups.edit', [
            'academicGroup' => $academicGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function update(AcademicGroupRequest $request, AcademicGroup $academicGroup)
    {
        $this->authorize('administrate');

        $academicGroup->update($request->validated());

        return to_route('academic-groups.show', ['academic_group' =>  $academicGroup])
            ->with('success', __('status.resource.updated', ['name' => $academicGroup->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicGroup $academicGroup)
    {
        $this->authorize('administrate');

        $academicGroup->delete();

        return to_route('academic-groups.index')
            ->with('success', __('status.resource.deleted', ['name' => $academicGroup->name]));
    }
}
