<?php

namespace App\Http\Controllers;

use App\Exceptions\NotEnoughQuestionsException;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\Team;
use App\Models\User;
use App\Support\Examiner;
use App\Models\Examination;
use App\Models\AcademicSubject;
use App\Jobs\GenerateExaminationJob;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ExaminationRequest;
use Illuminate\Support\Facades\Log;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
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

        $subtopics['essay'] = $academicSubject->essayQuestions()->with('subtopic')->get()->pluck('subtopic')->toArray();
        $subtopics['mcq'] = $academicSubject->mcqQuestions()->with('subtopic')->get()->pluck('subtopic')->toArray();
        $subtopics['trueFalse'] = $academicSubject->trueFalseQuestions()->with('subtopic')->get()->pluck('subtopic')->toArray();

        return view('examinations.create', [
            'academicSubject' => $academicSubject,
            'topics' => $topics,
            'metadata' => $metadata,
            'subtopics' => $subtopics
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws NotEnoughQuestionsException
     */
    public function store(AcademicSubject $academicSubject, ExaminationRequest $request)
    {
        dd($request->all());
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);



        $heading = $request->validated('heading');
        //$heading['duration'] = convertMinutesToHoursMinutes($heading['duration']);

        dd($request->all());
        $this->handle(
            $academicSubject,
            Team::query()->find($request->validated('team_id')),
            User::query()->find($request->validated('creator_id')),
            $request->validated('heading'),
            $request->validated('sections')
        );

        return to_route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject])
            ->with('success', __('status.exam.generating', ['title' => $heading['title']]));
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
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
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

    public function handle( AcademicSubject $academicSubject,
                            Team $team,
                            User $creator,
                            array $heading,
                            array $sections)
    {
        $multiple_choice_questions = [];
        $true_or_false_questions = [];
        $essay_questions = [];
        try {
            collect($sections)->each(function ($section) use (
                &$sections,
                &$multiple_choice_questions,
                &$true_or_false_questions,
                &$essay_questions
            ) {
                // TODO: validate that topics are under the right subject;
                $questions = DB::table($section['type'])
                    ->select('id')
                    ->whereIn('academic_topic_id', $section['topics'])
                    ->whereNotIn('id', ${$section['type']})
                    ->inRandomOrder()
                    ->take($section['count'])
                    ->get()
                    ->pluck('id')
                    ->all();

                if (count($questions) < $section['count']) {
                    throw new NotEnoughQuestionsException();
                }

                $sections[] = [
                    'name' => $section['name'],
                    'type' => $section['type'],
                    'questions' => $questions,
                ];

                ${$section['type']} = array_merge(${$section['type']}, $questions);
//                $heading['duration'] = convertMinutesToHoursMinutes($section['duration']);
            });
//            $heading['duration'] = convertMinutesToHoursMinutes($heading['duration']);
            $examination = new Examination([
                'title' => $heading['title'],
                'heading' => $heading,
                'sections' => $sections,
            ]);

            $examination->creator()->associate($creator);
            $examination->team()->associate($team);

            $academicSubject->examinations()->save($examination);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
