<?php

namespace App\Http\Controllers;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Http\Requests\AcademicSubjectRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;


class AcademicSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function index(AcademicGroup $academicGroup,  AcademicLevel $academicLevel, AcademicSubject $academicSubject)

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
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
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
     * @param AcademicLevel $academicLevel
     * @param AcademicSubjectRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubjectRequest $request): RedirectResponse
    {
        $this->authorize('administrate');

        $academicSubject = $academicLevel->academicSubjects()->create($request->validated());

        return to_route('academic-subjects.index', ['academic_group' => $academicGroup, 'academic_level' => $academicLevel])
            ->with('success', __('status.resource.created', ['name' => $academicSubject->name]));
    }


    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel,  AcademicSubject $academicSubject)
    {
        // Get paginated topics with subtopics count
        $topics = $academicSubject->academicTopics()
            ->withCount('subtopics')
            ->paginate(10); // 10 topics per page, adjust as needed

        return view('academic-subjects.show', compact('academicSubject', 'topics'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
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
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubjectRequest $request
     * @param AcademicSubject $academicSubject
     * @return RedirectResponse
     */
    public function update(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubjectRequest $request, AcademicSubject $academicSubject): RedirectResponse
    {
        $this->authorize('administrate');

        $academicSubject->update($request->validated());

        return to_route('academic-subjects.show', ['academic_subject' =>  $academicSubject])
            ->with('success', __('status.resource.updated', ['name' => $academicSubject->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @return RedirectResponse
     */
    public function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject): RedirectResponse
    {
        $this->authorize('administrate');

        $academicSubject->load('academicLevel')->delete();

        return to_route('academic-subjects.index', ['academic_group' => $academicGroup,'academic_level' => $academicSubject->academicLevel])
            ->with('success', __('status.resource.deleted', ['name' => $academicSubject->name]));
    }
}
