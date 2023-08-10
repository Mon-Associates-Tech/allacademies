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

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AcademicSubject $academicSubject)
    {
        $quizzes = $academicSubject->quizzes()->where('team_id', auth()->user()->current_team_id)->paginate();

        return view('quizzes.index', [
            'quizzes' => $quizzes,
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

        return view('quizzes.start', [
            'quiz' => $quiz,
            'academicSubject' => $quiz->academicSubject,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function take(Quiz $quiz, Request $request)
    {
        $worksheet = $quiz->worksheets()->firstOrCreate([
            'user_id' => auth()->id(),
        ], [
            'started_at' => now(),
            'seed' => mt_rand(),
        ]);

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
        $worksheet = $quiz->worksheets()->where('user_id', auth()->id())->firstOrFail();

        return view('quizzes.stop', [
            'quiz' => $quiz,
            'worksheet' => $worksheet,
            'academicSubject' => $quiz->academicSubject,
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        //
    }
}
