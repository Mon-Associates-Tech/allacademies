<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicTopicRequest;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AcademicTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
    {
        $academicTopics = $academicSubject->academicTopics()->latest('id')->paginate();

        $academicSubject->load('academicLevel.academicGroup');

        return view('academic-topics.index', [
            'academicTopics' => $academicTopics,
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');

        $academicSubject->load('academicLevel.academicGroup');

        return view('academic-topics.create', [
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopicRequest $request): RedirectResponse
    {
        $this->authorize('administrate');

        $academicTopic = $academicSubject->academicTopics()->create($request->validated());

        return to_route('academic-topics.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.created', ['name' => $academicTopic->name]));
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup')
            ->loadCount('multipleChoiceQuestions', 'trueOrFalseQuestions', 'essayQuestions');

        return view('academic-topics.show', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function edit(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('academic-topics.edit', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopicRequest $request, AcademicTopic $academicTopic): RedirectResponse
    {
        $this->authorize('administrate');

        $academicTopic->update($request->validated());

        return to_route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.updated', ['name' => $academicTopic->name]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, AcademicTopic $academicTopic): RedirectResponse
    {
        $this->authorize('administrate');

        $academicTopic->load('academicSubject')->delete();

        return to_route('academic-topics.index', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.resource.deleted', ['name' => $academicTopic->name]));
    }
}
