<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicLevelRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        // Load academic levels with additional counts for better display
        $academicLevels = $academicGroup->academicLevels()
            ->withCount('academicSubjects')
            ->latest('id')
            ->paginate(15);

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
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicGroup $academicGroup, AcademicLevelRequest $request)
    {
        $this->authorize('administrate');

        $academicLevel = $academicGroup->academicLevels()->create($request->validated());

        return to_route('academic-groups.index', ['academic_group' => $academicGroup])
            ->with('success', __('status.resource.created', ['name' => $academicLevel->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param AcademicLevel $academic_level
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel)
    {
        $this->authorize('moderate');

        $academicLevel->load([
            'academicGroup',
            'academicSubjects' => function($query) {
                $query->withCount('topics') // if topics relationship exists
                      ->orderBy('name');
            }
        ])->loadCount([
            'academicSubjects',
            'students', // if relationship exists
            'teachers'  // if relationship exists
        ]);

        return view('academic-levels.show', [
            'academicLevel' => $academicLevel,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AcademicLevel $academicLevel
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel)
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
     * @param AcademicGroup $academicGroup
     * @param AcademicLevelRequest $request
     * @param AcademicLevel $academicLevel
     * @return RedirectResponse
     */
    public function update(AcademicGroup $academicGroup, AcademicLevelRequest $request, AcademicLevel $academicLevel): RedirectResponse
    {
        $this->authorize('administrate');

        $academicLevel->update($request->validated());

        return to_route('academic-levels.show', ['academic_level' =>  $academicLevel])
            ->with('success', __('status.resource.updated', ['name' => $academicLevel->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @return RedirectResponse
     */
    public function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel): RedirectResponse
    {
        $this->authorize('administrate');

        $academicLevel->load('academicGroup')->delete();

        return to_route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup])
            ->with('success', __('status.resource.deleted', ['name' => $academicLevel->name]));
    }
}
