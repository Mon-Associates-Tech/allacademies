<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Jobs\GenerateQuizJob;
use App\Models\AcademicSubject;
use App\Http\Requests\QuizRequest;
use App\Models\Team;
use App\Models\User;
use App\Support\Quizzer;
use Illuminate\Support\Facades\Gate;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicSubject $academicSubject)
    {
        $this->authorize('subscribed', $academicSubject);

        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);
        $privileged = Gate::allows('privileged', $currentTeam);

        $quizzes = $academicSubject->quizzes()->where('team_id', auth()->user()->current_team_id)->latest('id')->paginate();

        return view('quizzes.index', [
            'quizzes' => $quizzes,
            'academicSubject' => $academicSubject,
            'privileged' => $privileged,
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

        return view('quizzes.create', [
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
    public function store(AcademicSubject $academicSubject, QuizRequest $request)
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

        return to_route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject])
            ->with('success', __('status.quiz.generating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function start(Quiz $quiz)
    {
        $quiz->load('academicSubject');

        $this->authorize('subscribed', $quiz->academicSubject);

        $creator = User::find($quiz->creator_id);
        $team = Team::find($quiz->team_id);

        Gate::allowIf(fn ($user) => $user->current_team_id === $quiz->team_id) && $this->isOwnerOrAdmin($creator, $team);

        $worksheet = $quiz->worksheets()->where('user_id', auth()->id())->first();

        if ($worksheet) {
            return to_route('quizzes.take', ['quiz' => $quiz]);
        }

        return view('quizzes.start', [
            'quiz' => $quiz,
            'academicSubject' => $quiz->academicSubject,
        ]);
    }

    private function isOwnerOrAdmin(User $user, Team $team): bool
    {
        // Check if the user is the owner of the team
        if ($team->owner_id === $user->id) {
            return true;
        }

        // Check if the user is an admin member of the team
        $isAdmin = $team->members()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();

        return $isAdmin;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function take(Quiz $quiz, Request $request)
    {
        $quiz->load('academicSubject');

        $this->authorize('subscribed', $quiz->academicSubject);
        $creator = User::find($quiz->creator_id);
        $team = Team::find($quiz->team_id);

        Gate::allowIf(fn ($user) => $user->current_team_id === $quiz->team_id) && $this->isOwnerOrAdmin($creator, $team);

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
            return to_route('quizzes.stop', ['quiz' => $quiz]);
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
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function stop(Quiz $quiz)
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
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function result(Quiz $quiz)
    {
        $quiz->load('academicSubject');
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);
        $this->authorize('subscribed', $quiz->academicSubject);
        $this->authorize('privileged', $currentTeam);

        $scores = [];
        $worksheets = $quiz->worksheets()->with('user')->where('quiz_id', $quiz->id)->paginate();
        foreach ($worksheets as $worksheet) {
            $score = Quizzer::getScore($quiz, $worksheet);
            $worksheet->score = $score;
            $scores[] = $score;
        }

        return view('quizzes.result', [
            'quiz' => $quiz,
            'worksheets' => $worksheets,
            'academicSubject' => $quiz->academicSubject,
            'score' => $scores[0],
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        $quiz->load('academicSubject');

        $this->authorize('subscribed', $quiz->academicSubject);
        Gate::allowIf(fn ($user) => $user->current_team_id === $quiz->team_id);

        $worksheet = $quiz->worksheets()->where('user_id', auth()->id())->first();

        if (!$worksheet || !$worksheet->ended_at) {
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
