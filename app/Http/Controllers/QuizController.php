<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Jobs\GenerateQuizJob;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Quiz;
use App\Models\Team;
use App\Models\User;
use App\Support\Quizzer;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
    {
        $this->authorize('subscribed', $academicSubject);

        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $quizzes = $academicSubject->quizzes()->where('team_id', auth()->user()->current_team_id)->latest('id')->paginate();

        return view('quizzes.index', [
            'quizzes' => $quizzes,
            'academicSubject' => $academicSubject,
            'currentTeam' => $currentTeam,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        $topics = $academicSubject->academicTopics()->select(['id', 'name'])->withCount(
            'multipleChoiceQuestions',
            'trueOrFalseQuestions',
            'essayQuestions',
        )->get()->toArray();

        return view('quizzes.create', [
            'academicSubject' => $academicSubject,
            'topics' => $topics,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, QuizRequest $request): RedirectResponse
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        dispatch(new GenerateQuizJob(
            $academicSubject,
            Team::query()->find($request->validated('team_id')),
            User::query()->find($request->validated('creator_id')),
            $request->validated('title'),
            $request->integer('duration_in_minutes'),
            null,
            null,
            $request->validated('sections')
        ));

        return to_route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
            ->with('success', __('status.quiz.generating'));
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|RedirectResponse|View
     */
    public function start(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, Quiz $quiz)
    {

        $this->authorize('subscribed', [$quiz->academicSubject]);

        Gate::allowIf(fn ($user) => $user->current_team_id === $quiz->team_id);

        $worksheet = $quiz->worksheets()->where('user_id', auth()->id())->first();

        if ($worksheet) {
            return to_route('quizzes.take', ['quiz' => $quiz, 'academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])
                ->with('info', __('status.quiz.already_started'));
        }

        return view('quizzes.start', [
            'quiz' => $quiz,
            'academicSubject' => $quiz->academicSubject,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|RedirectResponse|View
     */
    public function take(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, Quiz $quiz, Request $request)
    {
        $quiz->load('academicSubject');

        $this->authorize('subscribed', $quiz->academicSubject);

        Gate::allowIf(fn ($user) => $user->current_team_id === $quiz->team_id);

        $worksheet = $quiz->worksheets()->firstOrCreate([
            'user_id' => auth()->id(),
        ], [
            'started_at' => now(),
            'seed' => mt_rand(),
        ])->refresh();

        if ($request->isMethod('POST')) {
            $answer = $request->input('type') === 'true_or_false_questions'
                ? $request->boolean('answer')
                : $request->input('answer');
            Quizzer::markAnswer($quiz, $worksheet, $answer);
        }

        if (Quizzer::shouldStopWork($quiz, $worksheet)) {
            $worksheet->update(['ended_at' => now()]);
        }

        if ($worksheet->ended_at) {
            return to_route('quizzes.stop', ['quiz' => $quiz, 'academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]);
        }

        $question = Quizzer::askQuestion($quiz, $worksheet);

        return view('quizzes.take', [
            'quiz' => $quiz,
            'question' => $question,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function stop(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, Quiz $quiz)
    {
        $quiz->load('academicSubject');

        $this->authorize('subscribed', $quiz->academicSubject);
        Gate::allowIf(fn ($user) => $user->current_team_id === $quiz->team_id);

        $worksheet = $quiz->worksheets()->where('user_id', auth()->id())->firstOrFail();

        $score = Quizzer::getScore($quiz, $worksheet);

        return view('quizzes.stop', [
            'quiz' => $quiz,
            'worksheet' => $worksheet,
            'academicSubject' => $quiz->academicSubject,
            'score' => $score,
        ]);
    }

    /**
     * get results for a quiz
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function scores(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, Quiz $quiz)
    {
        $quiz->load('academicSubject');
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $quiz->academicSubject);
        $this->authorize('privileged', $currentTeam);

        $worksheets = $quiz->worksheets()
            ->with('user')
            ->where('quiz_id', $quiz->id)
            ->paginate();

        $worksheets->each(function ($worksheet) use ($quiz) {
            $worksheet->score = Quizzer::getScore($quiz, $worksheet);
        });

        return view('quizzes.scores', [
            'quiz' => $quiz,
            'worksheets' => $worksheets,
            'academicSubject' => $quiz->academicSubject,
            'score' => $worksheets->pluck('score')->first(),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|RedirectResponse|View
     */
    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, Quiz $quiz)
    {
        $quiz->load('academicSubject');

        $this->authorize('subscribed', $quiz->academicSubject);
        Gate::allowIf(fn ($user) => $user->current_team_id === $quiz->team_id);

        $worksheet = $quiz->worksheets()->where('user_id', auth()->id())->first();

        if (! $worksheet || ! $worksheet->ended_at) {
            return to_route('quizzes.take', ['quiz' => $quiz]);
        }

        $sections = Quizzer::createSections($quiz, $worksheet);
        $score = Quizzer::getScore($quiz, $worksheet);

        return view('quizzes.show', [
            'quiz' => $quiz,
            'worksheet' => $worksheet,
            'sections' => $sections,
            'academicSubject' => $quiz->academicSubject,
            'score' => $score,
        ]);
    }
}
