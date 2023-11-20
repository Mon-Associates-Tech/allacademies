<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Support\Examiner;
use App\Models\Examination;
use App\Models\AcademicSubject;
use App\Jobs\GenerateExaminationJob;
use App\Http\Requests\ExaminationRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicSubject $academicSubject)
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject) && $this->authorize('privileged', $currentTeam);

        $examinations = $academicSubject->examinations()->where('team_id', auth()->user()->current_team_id)->latest('id')->paginate();

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
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        $topics = $academicSubject->academicTopics()->select(['id', 'name'])->withCount(
            'multipleChoiceQuestions',
            'trueOrFalseQuestions',
            'essayQuestions',
        )->get()->toArray();

        $metadata =  data_get($currentTeam->meta, 'present', []);

        $logo = data_get($currentTeam->meta, 'logo');

        $metadata['logo'] = $logo ? Storage::disk('s3')->url($logo) : asset('img/logo.png');

        $metadata['subject_name'] = $academicSubject->name;
        $metadata['subject_code'] = $academicSubject->code;
        $metadata['level_name'] = $academicSubject->academicLevel->name;
        $metadata['level_label'] = $academicSubject->academicLevel->label;
        $metadata['group_name'] = $academicSubject->academicLevel->academicGroup->name;

        return view('examinations.create', [
            'academicSubject' => $academicSubject,
            'topics' => $topics,
            'metadata' => $metadata,
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
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        dispatch(new GenerateExaminationJob(
            $academicSubject,
            Team::query()->find($request->validated('team_id')),
            User::query()->find($request->validated('creator_id')),
            $request->validated('heading'),
            $request->validated('sections')
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
        $examination->load('academicSubject');

        $this->authorize('subscribed', $examination->academicSubject);
        Gate::allowIf(fn ($user) => $user->current_team_id === $examination->team_id);

        $sections = Examiner::createSections($examination);

        return view('examinations.show', [
            'examination' => $examination,
            'sections' => $sections,
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
        $examination->load('academicSubject');

        $this->authorize('subscribed', $examination->academicSubject);
        Gate::allowIf(fn ($user) => $user->current_team_id === $examination->team_id);

        $sections = Examiner::createSections($examination);

        return view('examinations.answer', [
            'examination' => $examination,
            'sections' => $sections,
        ]);
    }
}
