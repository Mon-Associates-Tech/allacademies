<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicLevelRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use Illuminate\Http\Request;

class AcademicLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('moderate');

        $academicLevels = AcademicLevel::query()->with('academicGroup')->get();

        return view('academic-levels.index', [
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

        $academicGroup->academicLevels()->create($request->validated());

        return to_route('academic-levels.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicLevel $academicLevel)
    {
        $this->authorize('administrate');
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicLevel $academicLevel)
    {
        $this->authorize('administrate');
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
    }
}
