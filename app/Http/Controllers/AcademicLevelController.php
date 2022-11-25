<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicLevelRequest;
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
        $academicLevels = AcademicLevel::all();

        return view('academic-levels.index', [
            'academicLevels' => $academicLevels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('academic-levels.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicLevelRequest $request)
    {
        AcademicLevel::query()->create($request->validated());

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicLevel $academicLevel)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicLevel  $academicLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicLevel $academicLevel)
    {
        //
    }
}
