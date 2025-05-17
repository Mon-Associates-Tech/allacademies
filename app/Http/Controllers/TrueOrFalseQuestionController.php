<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubtopic;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\AcademicTopic;
use App\Models\TrueOrFalseQuestion;
use App\Http\Requests\TrueOrFalseQuestionRequest;

class TrueOrFalseQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function index(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestions = $academicTopic->trueOrFalseQuestions()->with('academicTopic.academicSubject.academicLevel')->latest('id')->paginate();

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('true-or-false-questions.index', [
            'trueOrFalseQuestions' => $trueOrFalseQuestions,
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function create(AcademicTopic $academicTopic)
    {
        $this->authorize('moderate');

        $academicTopic->load('academicSubject.academicLevel.academicGroup');

        return view('true-or-false-questions.create', [
            'academicTopic' => $academicTopic,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicTopic $academicTopic
     * @param TrueOrFalseQuestionRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicTopic $academicTopic, TrueOrFalseQuestionRequest $request)
    {
        $this->authorize('moderate');

        $data = $request->validated();
        if(isset($request->subtopic)){
            $subTopic = AcademicSubtopic::firstOrCreate(
                ['name' => $request->subtopic],
                ['name' => $request->subtopic, 'academic_topic_id' => $academicTopic->id]
            );
            $data['academic_subtopic_id'] = $subTopic->id;
        }

        $trueOrFalseQuestion = $academicTopic->trueOrFalseQuestions()->create($data);

        return to_route('academic-topics.true-or-false-questions.index', ['academic_topic' => $academicTopic])
            ->with('success', __('status.resource.created', ['name' => $trueOrFalseQuestion->question->summary]));
    }

    /**
     * Display the specified resource.
     *
     * @param TrueOrFalseQuestion $trueOrFalseQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function show(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');
        $trueOrFalseQuestion->load('subtopic');

        return view('true-or-false-questions.show', [
            'trueOrFalseQuestion' => $trueOrFalseQuestion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TrueOrFalseQuestion $trueOrFalseQuestion
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function edit(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic.academicSubject.academicLevel.academicGroup');

        return view('true-or-false-questions.edit', [
            'trueOrFalseQuestion' => $trueOrFalseQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TrueOrFalseQuestionRequest $request
     * @param TrueOrFalseQuestion $trueOrFalseQuestion
     * @return RedirectResponse
     */
    public function update(TrueOrFalseQuestionRequest $request, TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');


        $subTopic = AcademicSubtopic::updateOrCreate(
            ['name' => $trueOrFalseQuestion->subtopic->name],
            ['name' => $request->subtopic, 'academic_topic_id' => $trueOrFalseQuestion->academic_topic_id]
        );
        $data = $request->validated();
        $data['academic_subtopic_id'] = $subTopic->id;

        $trueOrFalseQuestion->update($data);

        return to_route('true-or-false-questions.show', ['true_or_false_question' =>  $trueOrFalseQuestion])
            ->with('success', __('status.resource.updated', ['name' => $trueOrFalseQuestion->question->summary]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TrueOrFalseQuestion $trueOrFalseQuestion
     * @return RedirectResponse
     */
    public function destroy(TrueOrFalseQuestion $trueOrFalseQuestion)
    {
        $this->authorize('moderate');

        $trueOrFalseQuestion->load('academicTopic')->delete();

        return to_route('academic-topics.true-or-false-questions.index', ['academic_topic' => $trueOrFalseQuestion->academicTopic])
            ->with('success', __('status.resource.deleted', ['name' => $trueOrFalseQuestion->question->summary]));
    }
}
