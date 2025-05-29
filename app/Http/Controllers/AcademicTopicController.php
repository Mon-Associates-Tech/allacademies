<?php

namespace App\Http\Controllers;

use App\Models\AcademicTopic;
use App\Models\AcademicSubject;
use App\Http\Requests\AcademicTopicRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AcademicTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function index(AcademicSubject $academicSubject)
    {
        $this->authorize('moderate');

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
    public function create(AcademicSubject $academicSubject)
    {
//        $this->authorize('administrate');

        $academicSubject->load('academicLevel.academicGroup');

        return view('academic-topics.create', [
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicSubject $academicSubject
     * @param AcademicTopicRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicSubject $academicSubject, AcademicTopicRequest $request)
    {
        $this->authorize('administrate');

        $academicTopic = $academicSubject->academicTopics()->create($request->validated());

        return to_route('academic-subjects.academic-topics.index', ['academic_subject' => $academicSubject])
            ->with('success', __('status.resource.created', ['name' => $academicTopic->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param AcademicTopic $academicTopic
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(AcademicTopic $academicTopic)
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
     * @param AcademicTopic $academicTopic
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function edit(AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('academic-topics.edit', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AcademicTopicRequest $request
     * @param AcademicTopic $academicTopic
     * @return RedirectResponse
     */
    public function update(AcademicTopicRequest $request, AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');

        $academicTopic->update($request->validated());

        return to_route('academic-topics.show', ['academic_topic' =>  $academicTopic])
            ->with('success', __('status.resource.updated', ['name' => $academicTopic->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicTopic $academicTopic
     * @return RedirectResponse
     */
    public function destroy(AcademicTopic $academicTopic)
    {
        $this->authorize('administrate');

        $academicTopic->load('academicSubject')->delete();

        return to_route('academic-subjects.academic-topics.index', ['academic_subject' => $academicTopic->academicSubject])
            ->with('success', __('status.resource.deleted', ['name' => $academicTopic->name]));
    }
}
