<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Http\Requests\EssayQuestionRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;

class EssayQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function index(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $essayQuestions = $academicTopic->essayQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();
        $essayQuestions->load('subtopic');

        return view('essay-questions.index', [
            'essayQuestions' => $essayQuestions,
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicTopic $academicTopic
     * @param EssayQuestionRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicTopic $academicTopic, EssayQuestionRequest $request)
    {
        $this->authorize('moderate');
        $data = $request->validated();

        if (isset($request->subtopic)) {
            $subTopic = AcademicSubtopic::firstOrCreate(
                ['name' => $request->subtopic],
                ['name' => $request->subtopic, 'academic_topic_id' => $academicTopic->id]
            );
            $data['academic_subtopic_id'] = $subTopic->id;
        }

        $essayQuestion = $academicTopic->essayQuestions()->create($data);

        return to_route('academic-topics.essay-questions.index', ['academic_topic' => $academicTopic])
            ->with('success', __('status.resource.created', ['name' => $essayQuestion->question->summary]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function create(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        return view('essay-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param EssayQuestion $essayQuestion
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public
    function show(EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $essayQuestion->load('subtopic');

        return view('essay-questions.show', [
            'essayQuestion' => $essayQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EssayQuestion $essayQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public
    function edit(EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $essayQuestion->load('subtopic');

        return view('essay-questions.edit', [
            'essayQuestion' => $essayQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EssayQuestionRequest $request
     * @param EssayQuestion $essayQuestion
     * @return RedirectResponse
     */
    public
    function update(EssayQuestionRequest $request, EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $data = $request->validated();
        if (isset($request->subtopic) && isset($essayQuestion->subtopic->name)) {
            $subTopic = AcademicSubtopic::updateOrCreate(
                ['name' => $essayQuestion->subtopic->name],
                ['name' => $request->subtopic, 'academic_topic_id' => $essayQuestion->academic_topic_id]
            );
            $data['academic_subtopic_id'] = $subTopic->id;
        }

        $essayQuestion->update($data);

        return to_route('essay-questions.show', ['essay_question' => $essayQuestion])
            ->with('success', __('status.resource.updated', ['name' => $essayQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param EssayQuestion $essayQuestion
     * @return RedirectResponse
     */
    public
    function destroy(EssayQuestion $essayQuestion)
    {
        $this->authorize('moderate');

        $essayQuestion->load('academicTopic')->delete();

        return to_route('academic-topics.essay-questions.index', ['academic_topic' => $essayQuestion->academicTopic])
            ->with('success', __('status.resource.deleted', ['name' => $essayQuestion->question->summary]));
    }
}
