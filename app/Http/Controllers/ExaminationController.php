<?php

namespace App\Http\Controllers;

use App\Models\AcademicTopic;
use App\Models\Team;
use App\Models\User;
use App\Support\Examiner;
use App\Models\Examination;
use App\Models\AcademicSubject;
use App\Jobs\GenerateExaminationJob;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ExaminationRequest;
use Illuminate\Support\Facades\Storage;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function index(AcademicSubject $academicSubject)
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        $examinations = $academicSubject->examinations()->where('team_id', auth()->user()->current_team_id)->latest('id')->paginate();

        return view('examinations.index', [
            'examinations' => $examinations,
            'academicSubject' => $academicSubject,
            'currentTeam' => $currentTeam,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function create(AcademicSubject $academicSubject)
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

//        $topics = $academicSubject->academicTopics()->select(['id', 'name'])->with(
//        ['multipleChoiceQuestions',
//        'trueOrFalseQuestions',
//        'essayQuestions',]
//    )->withCount(
//            'multipleChoiceQuestions',
//            'trueOrFalseQuestions',
//            'essayQuestions',
//        )->get()->toArray();


        $metadata =  data_get($currentTeam->meta, 'present', []);

        $logo = data_get($currentTeam->meta, 'logo');

        $metadata['logo'] = $logo ? Storage::disk('s3')->url($logo) : asset('img/logo.png');

        $metadata['subject_name'] = $academicSubject->name;
        $metadata['subject_code'] = $academicSubject->code;
        $metadata['level_name'] = $academicSubject->academicLevel->name;
        $metadata['level_label'] = $academicSubject->academicLevel->label;
        $metadata['group_name'] = $academicSubject->academicLevel->academicGroup->name;

        $topics = AcademicTopic::where('academic_subject_id', $academicSubject->id)
            ->select(['id', 'name'])
            ->with([
                'subtopics' => function ($query) {
                    $query->withCount([
                        'essayQuestions',
                        'multipleChoiceQuestions',
                        'trueOrFalseQuestions'
                    ]);
                }
            ])
            ->get()
            ->map(function ($topic) {
                $subtopics = $topic->subtopics->map(function ($subtopic) {
                    return [
                        'id' => $subtopic->id,
                        'name' => $subtopic->name,
                        'essay_questions_count' => $subtopic->essay_questions_count,
                        'multiple_choice_questions_count' => $subtopic->multiple_choice_questions_count,
                        'true_or_false_questions_count' => $subtopic->true_or_false_questions_count,
                    ];
                });

                $questionsCount = $subtopics->sum(function ($sub) {
                    return $sub['essay_questions_count']
                        + $sub['multiple_choice_questions_count']
                        + $sub['true_or_false_questions_count'];
                });

                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'questions_count' => $questionsCount,
                    'subtopics' => $subtopics->toArray(),
                ];
            })
            ->toArray();


        return view('examinations.create', [
            'academicSubject' => $academicSubject,
            'topics' => $topics,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicSubject $academicSubject
     * @param ExaminationRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicSubject $academicSubject, ExaminationRequest $request)
    {

        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        $heading = $request->validated('heading');

        GenerateExaminationJob::dispatch(
            $academicSubject,
            Team::query()->find($request->validated('team_id')),
            User::query()->find($request->validated('creator_id')),
            $heading,
            $request->validated('sections')
        );

        return to_route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject])
            ->with('success', __('status.exam.generating', ['title' => $heading['title']]));
    }

    /**
     * Display the specified resource.
     *
     * @param Examination $examination
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function show(Examination $examination)
    {
        $examination->load('academicSubject');
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $examination->academicSubject);
        $this->authorize('privileged', $currentTeam);

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
     * @param Examination $examination
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function answers(Examination $examination)
    {
        $examination->load('academicSubject');
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $examination->academicSubject);
        $this->authorize('privileged', $currentTeam);

        Gate::allowIf(fn ($user) => $user->current_team_id === $examination->team_id);

        $sections = Examiner::createSections($examination);

        return view('examinations.answer', [
            'examination' => $examination,
            'sections' => $sections,
        ]);
    }
}
