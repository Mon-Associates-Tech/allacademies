<?php

namespace App\Http\Controllers;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\Team;
use App\Models\User;
use App\Services\QuestionGenerator;
use App\Support\Examiner;
use App\Support\Mark;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Log;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function index(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
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
    public function create(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject)
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        session()?->forget('examination_preview_data');


        $metadata = data_get($currentTeam->meta, 'present', []);

        $logo = data_get($currentTeam->meta, 'logo');

        $metadata['logo'] = $logo ? Storage::disk('local')->url($logo) : asset('img/logo.png');

        $metadata['subject_name'] = $academicSubject->name;
        $metadata['subject_code'] = $academicSubject->code;
        $metadata['level_name'] = $academicSubject->academicLevel->name;
        $metadata['level_label'] = $academicSubject->academicLevel->label;
        $metadata['group_name'] = $academicSubject->academicLevel->academicGroup->name;

        $topics = AcademicTopic::where('academic_subject_id', $academicSubject->id)
            ->select(['id', 'name'])
            ->withCount([
                'essayQuestions',
                'multipleChoiceQuestions',
                'trueOrFalseQuestions'
            ])
            ->with([
                'subtopics' => function ($query) {
                    $query->withCount([
                        'essayQuestions',
                        'multipleChoiceQuestions',
                        'trueOrFalseQuestions',
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

                $questionsCount = $subtopics->isEmpty()
                    ? ($topic->essay_questions_count + $topic->multiple_choice_questions_count + $topic->true_or_false_questions_count)
                    : $subtopics->sum(function ($sub) {
                        return $sub['essay_questions_count']
                            + $sub['multiple_choice_questions_count']
                            + $sub['true_or_false_questions_count'];
                    });

                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'questions_count' => $questionsCount,
                    'essay_questions_count' => $subtopics->isEmpty()
                        ? $topic->essay_questions_count
                        : $subtopics->sum('essay_questions_count'),
                    'multiple_choice_questions_count' => $subtopics->isEmpty()
                        ? $topic->multiple_choice_questions_count
                        : $subtopics->sum('multiple_choice_questions_count'),
                    'true_or_false_questions_count' => $subtopics->isEmpty()
                        ? $topic->true_or_false_questions_count
                        : $subtopics->sum('true_or_false_questions_count'),
                    'subtopics' => $subtopics->toArray(),
                    'selectedOptions' => []
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
     * Display the specified resource.
     *
     * @param Examination $examination
     * @return Application|Factory|View|\Illuminate\View\View
     * @throws \ImagickException
     */
    public function show(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, Examination $examination)
    {
        $examination->load('academicSubject');
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $examination->academicSubject);
        $this->authorize('privileged', $currentTeam);

        Gate::allowIf(static fn($user) => $user->current_team_id === $examination->team_id);

        $previewData = [
            'team_id' => $examination->team_id,
            'creator_id' => $examination->creator_id,
            'academic_subject' => $examination->academicSubject,
        ];


        return view('examinations.show', [
            'examination' => $examination,
            'previewData' => $previewData,
            'academicSubject' => $examination->academicSubject,
            'sections' => $this->formatSection($examination->sections),
            'heading' => $examination->heading instanceof Mark
                ? $examination->heading->toArray()
                : $examination->heading
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param Examination $examination
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function answers(AcademicGroup $academicGroup, AcademicLevel $academicLevel, AcademicSubject $academicSubject, Examination $examination)
    {
        $examination->load('academicSubject');
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $examination->academicSubject);
        $this->authorize('privileged', $currentTeam);

        Gate::allowIf(static fn($user) => $user->current_team_id === $examination->team_id);

        $sections = Examiner::createSections($examination);

        return view('examinations.answer', [
            'examination' => $examination,
            'heading' => $examination->heading->html,
            'sections' => $sections,
        ]);
    }

    /**
     * Generate a preview of the examination without saving to a database
     */
    public function generatePreview(HttpRequest $request, AcademicSubject $academicSubject): ?RedirectResponse
    {
        try {
            $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

            $this->authorize('subscribed', $academicSubject);
            $this->authorize('privileged', $currentTeam);

            $metadata = json_decode(base64_decode($request['metadata']), true, 512, JSON_THROW_ON_ERROR);

            $preprocessedSections = QuestionGenerator::preprocessSections($request['sections']);

//            $previewData = QuestionGenerator::generate($request['heading'], $request['sections'], $metadata);
            $previewData = QuestionGenerator::generate($request['heading'], $preprocessedSections, $metadata);

            $previewData['sections'] = array_filter($previewData['sections'], static function ($data) {
                return !array_key_exists('count', $data);
            });

            $previewData['creator_id'] = auth()->user()->current_team_id;
            $previewData['team_id'] = $currentTeam->id;
            $previewData['metadata'] = $metadata;

            session(['examination_preview' => $previewData]);

            return redirect()->route('examinations.preview', [
                'academic_subject' => $academicSubject,
            ]);

        } catch (Exception $e) {
            Log::error('Preview generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['general' => 'Failed to generate preview: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the examination preview
     */
    public function preview(AcademicSubject $academicSubject, HttpRequest $request)
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        $previewData = session('examination_preview');

        if (!$previewData) {
            return redirect()->route('examinations.create', ['academic_subject' => $academicSubject])
                ->withErrors(['general' => 'No preview data found. Please generate an examination first.']);
        }

        $previewData['creator_id'] = auth()->user()->current_team_id;
        $previewData['team_id'] = $currentTeam->id;

        $previewData['sections'] = $this->formatSection($previewData['sections']);

        return view('examinations.preview', [
            'academicSubject' => $academicSubject,
            'heading' => $previewData['heading'],
            'sections' => $previewData['sections'],
            'request' => $request,
            'metadata' => $previewData['metadata'],
            'title' => $previewData['title'],
            'previewData' => $previewData,
        ]);
    }

    private function formatSection($sections): array
    {
        $questionGenerator = new QuestionGenerator();
        $previewData = $questionGenerator->processSections($sections);
        $previewData['sections'] = $questionGenerator->normalizeQuestionsToIds($sections);


        return collect($previewData['sections'])->map(function ($section) use ($questionGenerator) {
            if (!empty($section['questions'])) {
                $questionType = str_replace('_questions', '', $section['type']);
                $questionIds = $questionGenerator->getQuestionIdsOnly($section['questions']);
                $section['questions'] = $questionGenerator->fetchCompleteQuestions($questionIds, $questionType);
                $section['questions'] = $questionGenerator->formatExistingQuestions($section['questions']);
            }
            return $section;
        })->toArray();
    }

    /**
     * Store the examination after preview confirmation
     */
    public function store(AcademicSubject $academicSubject, HttpRequest $request): ?RedirectResponse
    {
        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        $validatedData = request()->all();
        $team = Team::query()->findOrFail($request->team_id);
        $creator = User::query()->findOrFail($request->creator_id);

        try {
            $examinationService = new QuestionGenerator();
            $examinationService->createExamination(
                $academicSubject,
                $validatedData,
                $team->id,
                $creator->id
            );

            // Clear preview data from the session
            session()?->forget('examination_preview_data');

            return redirect()
                ->route('academic-subjects.examinations.index', $academicSubject)
                ->with('success', 'Examination is being generated! You will be notified when it\'s ready.');

        } catch (Exception $e) {
            return back()
                ->withErrors(['general' => 'Failed to create examination. Please try again.', 'error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * @param mixed $sections
     * @return mixed
     */

}
