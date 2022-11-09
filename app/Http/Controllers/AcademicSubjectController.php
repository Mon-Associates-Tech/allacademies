<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicSubjectRequest;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use Illuminate\Http\Request;

class AcademicSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $academicSubjects = AcademicSubject::query()->with('academicLevel')->get();

        return view('academic-subjects.index', [
            'academicSubjects' => $academicSubjects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicLevel $academicLevel)
    {
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
        $academicLevel->academicSubjects()->create($request->validated());

        return redirect()->route('academic-subjects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicSubject $academicSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicSubject $academicSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicSubject $academicSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicSubject $academicSubject)
    {
        //
    }
}
