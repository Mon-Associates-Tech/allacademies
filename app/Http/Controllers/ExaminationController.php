<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\MetaData;
use App\Support\Examiner;
use App\Enums\TeamStatus;
use App\Models\Examination;
use App\Models\Subscription;
use App\Models\AcademicSubject;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use App\Jobs\GenerateExaminationJob;
use App\Http\Requests\ExaminationRequest;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicSubject $academicSubject)
    {
        $team = Team::find(auth()->user()->current_team_id);
        $canCreate = ((!$team->is_personal && $team->status === TeamStatus::APPROVED) || ($team->is_personal));

        $examinations = $academicSubject->examinations()->where('team_id', auth()->user()->current_team_id)->paginate();
        return view('examinations.index', [
            'examinations' => $examinations,
            'academicSubject' => $academicSubject,
            'canCreate' => $canCreate,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicSubject $academicSubject)
    {
        $team_status = Team::find(auth()->user()->current_team_id);
        abort_unless((!$team_status->is_personal && $team_status->status === TeamStatus::APPROVED) || ($team_status->is_personal), 403, 'Provide institution details under edit teams and these details must be approved before you create examinations.');

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
        $team_status = Team::find(auth()->user()->current_team_id);
        abort_unless((!$team_status->is_personal && $team_status->status === TeamStatus::APPROVED) || ($team_status->is_personal), 403, 'Provide institution details under edit teams and these details must be approved before you create examinations.');

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
        $sections = Examiner::createSections($examination);

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
        $academicSubject = $examination->academicSubject()->with('academicLevel.academicGroup')->first();
        $sections = Examiner::createSections($examination);

        return view('examinations.answer', [
            'examination' => $examination,
            'sections' => $sections,
            'academicSubject' => $academicSubject,
        ]);
    }
}
