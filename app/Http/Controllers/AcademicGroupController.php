<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicGroupRequest;
use App\Models\AcademicGroup;
use Illuminate\Http\Request;

class AcademicGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('administrate');

        $academicGroups = AcademicGroup::all();

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

        AcademicGroup::query()->create($request->validated());

        return to_route('academic-groups.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicGroup $academicGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicGroup $academicGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicGroup $academicGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicGroup $academicGroup)
    {
        //
    }
}
