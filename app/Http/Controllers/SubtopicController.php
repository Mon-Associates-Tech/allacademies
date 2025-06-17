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
    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject,  AcademicTopic $academicTopic)
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
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic, Request $request): RedirectResponse
    {

        $this->authorize('administrate');

        $subtopic = $academicTopic->subtopics()->create($request->all());

        return to_route('subtopics.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group') ])
            ->with('success', __('status.resource.created', ['name' => $academicTopic->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @param AcademicLevel $academicLevel
     * @param AcademicSubject $academicSubject
     * @param AcademicTopic $academicTopic
     * @param AcademicSubtopic $subtopic
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicTopic $academic_topic
     * @param AcademicSubtopic $academic_subtopic
     * @return RedirectResponse
     */
    public function destroy(AcademicTopic $academic_topic, AcademicSubtopic $academic_subtopic)
    {
        $this->authorize('administrate');
        $academic_subtopic->delete();

        return to_route('academic-topics.subtopics.index', ['academic_topic' => $academic_topic])
            ->with('success', __('status.resource.deleted', ['name' => $academic_subtopic->name]));
    }
}
