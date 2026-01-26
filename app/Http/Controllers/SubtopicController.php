<?php

namespace App\Http\Controllers;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubtopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $subtopics = $academicTopic->subtopics()->latest('id')->paginate();

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('academic-subtopics.index', [
            'subtopics' => $subtopics,
            'academic_topic' => $academicTopic,
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');

        $academicTopic->load('subtopics');

        return view('academic-subtopics.create', [
            'academic_topic' => $academicTopic,
            'academicSubject' => $academicTopic->academicSubject,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, Request $request): RedirectResponse
    {

        $this->authorize('administrate');

        $subtopic = $academicTopic->subtopics()->create($request->all());

        return to_route('subtopics.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.created', ['name' => $academicTopic->name]));
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, AcademicSubtopic $subtopic)
    {
        $this->authorize('moderate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('academic-subtopics.show', [
            'academic_subtopic' => $subtopic,
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|object|View
     */
    public function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, AcademicSubtopic $subtopic)
    {
        return view('academic-subtopics.edit', ['subtopic' => $subtopic, 'academic_topic' => $academicTopic, 'academicSubject' => $academicTopic->academicSubject]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, AcademicSubtopic $subtopic, Request $request)
    {
        $this->authorize('administrate');
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $subtopic->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]));

        return to_route('subtopics.index', [
            'academic_topic' => $academicTopic,
            'academic_subject' => $academicSubject,
            'academic_level' => $academicLevel,
            'academic_group' => $academicGroup,
        ])->with('success', __('status.resource.updated', ['name' => $subtopic->name]));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, AcademicSubtopic $subtopic)
    {
        $this->authorize('administrate');
        $subtopic->delete();

        return to_route('subtopics.index', [
            'academic_topic' => $academicTopic,
            'academic_subject' => $academicSubject,
            'academic_level' => $academicLevel,
            'academic_group' => $academicGroup,
        ])
            ->with('success', __('status.resource.deleted', ['name' => $subtopic->name]));
    }
}
