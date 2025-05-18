<?php

namespace App\Http\Controllers;

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
    public function index(AcademicTopic $academic_topic)
    {
        $this->authorize('moderate');

        $subtopics = $academic_topic->subtopics()->latest('id')->paginate();

        $academic_topic->load('academicSubject.academicLevel.academicGroup');

        return view('academic-subtopics.index', [
            'subtopics' => $subtopics,
            'academic_topic' => $academic_topic,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function create(AcademicTopic $academic_topic)
    {
        $this->authorize('administrate');

//        $academic_topic->load('academic_subtopics');

        return view('academic-subtopics.create', [
            'academic_topic' => $academic_topic,
            'academicSubject' => $academic_topic->academicSubject,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function store(AcademicTopic $academic_topic, Request $request)
    {

        $this->authorize('administrate');

        $subtopic = $academic_topic->subtopics()->create($request->all());

        return to_route('academic-topics.subtopics.index', ['academic_topic' => $academic_topic])
            ->with('success', __('status.resource.created', ['name' => $academic_topic->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param AcademicSubtopic $academic_subtopic
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(AcademicTopic $academic_topic, AcademicSubtopic $subtopic)
    {
        $this->authorize('moderate');

        $academic_topic->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('academic-subtopics.show', [
            'academic_subtopic' => $subtopic,
            'academic_topic' => $academic_topic,
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
     * @param  \Illuminate\Http\Request  $request
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
