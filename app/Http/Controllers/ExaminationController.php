<?php

namespace App\Http\Controllers;

use App\Exceptions\NotEnoughQuestionsException;
use App\Models\AcademicTopic;
use App\Models\Team;
use App\Models\User;
use App\Support\Examiner;
use App\Models\Examination;
use App\Models\AcademicSubject;
use App\Jobs\GenerateExaminationJob;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ExaminationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use \Imagick;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;

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


        $metadata = data_get($currentTeam->meta, 'present', []);

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
     * Store a newly created resource in storage.
     *
     * @param AcademicSubject $academicSubject
     * @param ExaminationRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicSubject $academicSubject, HttpRequest $request)
    {

        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $this->authorize('subscribed', $academicSubject);
        $this->authorize('privileged', $currentTeam);

        $heading = $request->heading;

        $this->handle(
            $academicSubject,
            Team::query()->find($request->team_id),
            User::query()->find($request->creator_id),
            $heading,
            $request->sections
        );

//        GenerateExaminationJob::dispatch(
//            $academicSubject,
//            Team::query()->find($request->team_id),
//            User::query()->find($request->creator_id),
//            $heading,
//            $request->sections
//        );

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

        Gate::allowIf(fn($user) => $user->current_team_id === $examination->team_id);

        $sections = Examiner::createSections($examination);

        foreach ($sections as $index => $section) {
            $path = storage_path('app/public/' . $section['document']);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            $sections[$index]['extension'] = $ext;
            $sections[$index]['original_path'] = $section['document'];
            $sections[$index]['pdf_images'] = [];

            if (in_array($ext, ['doc', 'docx']) && file_exists($path)) {
                $phpWord = IOFactory::load($path, 'Word2007');
                $docxText = '';

                foreach ($phpWord->getSections() as $sec) {
                    foreach ($sec->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $docxText .= $element->getText() . "\n";
                        }
                    }
                }

                $sections[$index]['document'] = $docxText;
            }

            $pdfPath = storage_path('app/public/' . $sections[$index]['original_path']);
            $images = [];

            if (file_exists($pdfPath)) {
                $outputDir = storage_path('app/public/pdf_pages');

                if (!file_exists($outputDir)) {
                    mkdir($outputDir, 0755, true);
                }

                $imagick = new \Imagick();
                $imagick->setResolution(300, 300);
                $imagick->readImage($pdfPath);

                foreach ($imagick as $i => $page) {
                    $page->setImageFormat('jpg');
                    $filename = 'pdf_page_' . $i  . '.jpg';
                    $outputPath = $outputDir . '/' . $filename;

                    $page->writeImage($outputPath);

                    $images[] = 'pdf_pages/' . $filename; // relative path for Blade
                }

                $sections[$index]['pdf_images'] = $images;
            }

        }


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

        Gate::allowIf(fn($user) => $user->current_team_id === $examination->team_id);

        $sections = Examiner::createSections($examination);

        return view('examinations.answer', [
            'examination' => $examination,
            'sections' => $sections,
        ]);
    }

    public function handle(
         AcademicSubject $academicSubject,
         Team $team,
         User $creator,
         array $heading,
         array $sections
    ): void
    {

            $allQuestions = [];

            foreach ($sections as $section) {
                $topicIds = collect($section['topics'])->map(fn($id) => (int) $id)->all();
                $subtopics = $section['subtopics'];
                $table = $section['type'];

                if ($section['document'] instanceof UploadedFile) {
                    $file = $section['document'];
                    $path = $file->store('documents', 'public');
                    $section['document'] = $path;
                }

                foreach ($subtopics as $subtopic) {
                    $count = (int) $subtopic['count'];

                    $questions = DB::table($table)
                        ->join('academic_subtopics', $table . '.academic_subtopic_id', '=', 'academic_subtopics.id')

                        ->whereIn('academic_subtopics.academic_topic_id', $topicIds)
                        ->inRandomOrder()
                        ->select($table . '.id')
                        ->take($count)
                        ->get()
                        ->pluck('id');


                    $allQuestions = array_merge($allQuestions, $questions->all());
                }
                $page = isset($section['page']) ? '<div class="h-screen w-full bg-white print:break-before-page print:break-after-page"></div>' : null;




                $sections[] = [
                    'name' => $section['name'],
                    'type' => $section['type'],
                    'questions' => $allQuestions,
                    'page' => $page,
                    'document' => $section['document'],
                    'instructions' => $section['instructions'],
                ];


            }

            array_shift($sections);

            $examination = new Examination([
                'title' => $heading['title'],
                'heading' => $heading,
                'sections' => $sections,
            ]);

            $examination->creator()->associate($creator);
            $examination->team()->associate($team);

            $academicSubject->examinations()->save($examination);

    }
}
