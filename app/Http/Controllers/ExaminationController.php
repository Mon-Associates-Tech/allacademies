<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Examination;
use Illuminate\Http\Request;
use App\Models\AcademicSubject;
use App\Jobs\GenerateExaminationJob;
use Illuminate\Support\Facades\Gate;
use App\Models\MultipleChoiceQuestion;
use App\Http\Requests\ExaminationRequest;
use App\Models\EssayQuestion;
use App\Models\TrueOrFalseQuestion;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::check('administrate')) {
            $examinations = Examination::query()->with('academicSubject.academicLevel')->get();
        } else {
            $examinations = Examination::query()->with('academicSubject.academicLevel')->where('team_id', auth()->user()->current_team_id)->get();
        }


        return view('examinations.index', [
            'examinations' => $examinations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicSubject $academicSubject)
    {
        $topics = $academicSubject->academicTopics()->select(['id', 'name'])->withCount(
            'multipleChoiceQuestions',
            'trueOrFalseQuestions',
            'essayQuestions',
        )->get()->toArray();

        return view('examinations.create', [
            'academicSubject' => $academicSubject,
            'topics' => $topics,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicSubject $academicSubject, ExaminationRequest $request)
    {
        dispatch(new GenerateExaminationJob(
            $academicSubject,
            Team::query()->find($request->validated('team_id')),
            User::query()->find($request->validated('creator_id')),
            $request->validated('title'),
            $request->validated('heading'),
            $request->validated('sections'),
            $request->validated('examiners')
        ));

        return to_route('examinations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function show(Examination $examination)
    {
        $sections = array_map(function ($section) {
            $questions = collect();
            if ('multiple_choice_questions' === $section['type']) {
                $questions = MultipleChoiceQuestion::query()->find($section['questions']);
            }

            if ('true_or_false_questions' === $section['type']) {
                $questions = TrueOrFalseQuestion::query()->find($section['questions']);
            }

            if ('essay_questions' === $section['type']) {
                $questions = EssayQuestion::query()->find($section['questions']);
            }

            return [
                ...$section,
                'questions' => $questions
            ];
        }, $examination->sections);

        return view('examinations.show', [
            'examination' => $examination,
            'sections' => $sections,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function edit(Examination $examination)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Examination $examination)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function destroy(Examination $examination)
    {
        //
    }
}
