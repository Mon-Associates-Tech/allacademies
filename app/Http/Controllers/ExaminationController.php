<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Examination;
use Illuminate\Http\Request;
use App\Models\AcademicSubject;
use App\Jobs\GenerateExaminationJob;
use App\Models\MultipleChoiceQuestion;
use App\Http\Requests\ExaminationRequest;
use App\Models\EssayQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Models\MetaData;
use App\Models\Subscription;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicSubject $academicSubject)
    {
        $examinations = $academicSubject->examinations()->with('metaData')->where('team_id', auth()->user()->current_team_id)->paginate();
        return view('examinations.index', [
            'examinations' => $examinations,
            'academicSubject' => $academicSubject,
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

        $package = Subscription::where('package', SubscriptionPackage::INSTITUTION_FULL)->where('status', SubscriptionStatus::PAID)->where('team_id', auth()->user()->current_team_id)->select('package')->first();
        $academicLevel = $academicSubject->academicLevel()->with('academicGroup')->first();
        $metaData = MetaData::where('team_id', auth()->user()->current_team_id)->with('team')->first();
        return view('examinations.create', [
            'academicSubject' => $academicSubject,
            'topics' => $topics,
            'academicLevel' => $academicLevel,
            'metaData' => $metaData,
            'package' => $package,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicSubject $academicSubject, Subscription $package, ExaminationRequest $request)
    {
        dispatch(new GenerateExaminationJob(
            $academicSubject,
            Team::query()->find($request->validated('team_id')),
            User::query()->find($request->validated('creator_id')),
            $request->validated('heading_type'),
            $request->validated('title'),
            $request->validated('date'),
            $request->validated('start'),
            $request->validated('end'),
            $request->validated('instructions'),
            $request->validated('sections'),
            $request->validated('examiners'),
        ));

        return to_route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject])
            ->with('success', __('status.exam.generating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function show(Examination $examination)
    {
        $academicSubject = $examination->academicSubject()->with('academicLevel.academicGroup')->first();
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
            'academicSubject' => $academicSubject,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function answers(Examination $examination)
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

        return view('examinations.answer', [
            'examination' => $examination,
            'sections' => $sections,
        ]);
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
