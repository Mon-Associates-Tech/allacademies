<?php

namespace App\MockExam\Controllers;

use App\Exceptions\NotEnoughQuestionsException;
use App\Exceptions\NoTopicsException;
use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\Team;
use App\MockExam\Models\MockExamSubscription;
use App\MockExam\Services\MockExamCreationService;
use App\Services\QuestionGenerator;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MockExamCustomExamController extends Controller
{
    public function __construct(
        private readonly MockExamCreationService $creationService,
    ) {
        // Open to any authenticated user — subscription gate is checked per action.
    }

    /**
     * Show the subject selection page for custom exam generation.
     */
    public function index(): View
    {
        $subscriptions = MockExamSubscription::where('user_id', auth()->id())
            ->active()
            ->with('subjects')
            ->get()
            ->filter(fn (MockExamSubscription $sub) => $sub->canCreateExam());

        $subjects = $subscriptions->flatMap(fn ($sub) => $sub->subjects)->unique('id')->values();

        return view('mock-exam.custom-exam.index', [
            'subscriptions' => $subscriptions,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show the heading + sections configuration form for the selected subject.
     */
    public function configure(AcademicSubject $academicSubject): View
    {
        // Gate: user must have an active subscription covering this subject
        $this->ensureActiveSubscription($academicSubject);

        session()->forget('custom_exam_preview_data');

        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

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
            ->with([
                'subtopics' => function ($query) {
                    $query->select(['id', 'name', 'academic_topic_id']);
                },
            ])
            ->get()
            ->map(function ($topic) {
                // Count questions at subtopic level
                $subtopics = $topic->subtopics->map(function ($subtopic) {
                    return [
                        'id' => $subtopic->id,
                        'name' => $subtopic->name,
                        'essay_questions_count' => $subtopic->essayQuestions()->count(),
                        'multiple_choice_questions_count' => $subtopic->multipleChoiceQuestions()->count(),
                        'true_or_false_questions_count' => $subtopic->trueOrFalseQuestions()->count(),
                    ];
                });

                // Count questions at topic level
                $topicEssayCount = \DB::table('essay_questions')
                    ->where('academic_topic_id', $topic->id)
                    ->count();
                $topicMcqCount = \DB::table('multiple_choice_questions')
                    ->where('academic_topic_id', $topic->id)
                    ->count();
                $topicTofCount = \DB::table('true_or_false_questions')
                    ->where('academic_topic_id', $topic->id)
                    ->count();

                $totalQuestionsCount = $topicEssayCount + $topicMcqCount + $topicTofCount;

                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'questions_count' => $totalQuestionsCount,
                    'essay_questions_count' => $topicEssayCount,
                    'multiple_choice_questions_count' => $topicMcqCount,
                    'true_or_false_questions_count' => $topicTofCount,
                    'subtopics' => $subtopics->toArray(),
                    'selectedOptions' => [],
                ];
            })
            ->toArray();

        return view('mock-exam.custom-exam.configure', [
            'academicSubject' => $academicSubject,
            'topics' => $topics,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Generate a preview of the custom exam without persisting it.
     */
    public function preview(Request $request, AcademicSubject $academicSubject): ?RedirectResponse
    {
        try {
            // Gate: user must have an active subscription covering this subject
            $this->ensureActiveSubscription($academicSubject);

            $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

            // Store form data in session before processing
            $headingData = $request['heading'];
            
            // Normalize heading fields that may be arrays due to Livewire wire:model on named form fields
            if (isset($headingData['instructions']) && is_array($headingData['instructions'])) {
                $headingData['instructions'] = $headingData['instructions']['down'] ?? $headingData['instructions']['up'] ?? '';
            }
            if (isset($headingData['title']) && is_array($headingData['title'])) {
                $headingData['title'] = is_string($headingData['title'][0] ?? '') ? $headingData['title'][0] : implode(' ', array_filter($headingData['title'], 'is_string'));
            }
            if (isset($headingData['duration']) && is_array($headingData['duration'])) {
                $headingData['duration'] = is_string($headingData['duration'][0] ?? '') ? $headingData['duration'][0] : implode(' ', array_filter($headingData['duration'], 'is_string'));
            }

            session(['custom_exam_form_data' => [
                'heading' => $headingData,
                'sections' => $request['sections'],
            ]]);

            $metadata = json_decode(base64_decode($request['metadata']), true, 512, JSON_THROW_ON_ERROR);

            $preprocessedSections = QuestionGenerator::preprocessSections($request['sections']);

            $previewData = QuestionGenerator::generate($headingData, $preprocessedSections, $metadata);

            $previewData['sections'] = array_filter($previewData['sections'], static function ($data) {
                return ! array_key_exists('count', $data);
            });

            $previewData['creator_id'] = auth()->user()->id;
            $previewData['team_id'] = $currentTeam->id;
            $previewData['metadata'] = $metadata;
            $previewData['academic_subject_id'] = $academicSubject->id;

            session(['custom_exam_preview' => $previewData]);

            // Clear form data on success
            session()->forget('custom_exam_form_data');

            return redirect()->route('mock-exams.generate.custom.preview.show', [
                'academicSubject' => $academicSubject,
            ]);

        } catch (NotEnoughQuestionsException $e) {
            return back()
                ->withInput($request->all())
                ->withErrors([
                    'questions' => 'Not enough questions available. Please reduce the number of questions or select additional topics.',
                    'details' => $e->getMessage(),
                ]);
        } catch (NoTopicsException $e) {
            return back()
                ->withInput($request->all())
                ->withErrors([
                    'topics' => 'No topics selected. Please select at least one topic for each section.',
                    'details' => $e->getMessage(),
                ]);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Custom exam preview generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput($request->all())
                ->withErrors(['general' => 'Failed to generate preview: '.$e->getMessage()]);
        }
    }

    /**
     * Show the preview page for the custom exam.
     */
    public function showPreview(AcademicSubject $academicSubject, Request $request): View
    {
        // Gate: user must have an active subscription covering this subject
        $this->ensureActiveSubscription($academicSubject);

        $currentTeam = Team::query()->findOrFail(auth()->user()->current_team_id);

        $previewData = session('custom_exam_preview');

        if (! $previewData) {
            return redirect()->route('mock-exams.generate.custom.configure', [
                'academicSubject' => $academicSubject,
            ])
                ->withErrors(['general' => 'No preview data found. Please configure your exam first.']);
        }

        $previewData['creator_id'] = auth()->user()->id;
        $previewData['team_id'] = $currentTeam->id;

        $previewData['sections'] = $this->formatSections($previewData['sections']);

        return view('mock-exam.custom-exam.preview', [
            'academicSubject' => $academicSubject,
            'heading' => $previewData['heading'],
            'sections' => $previewData['sections'],
            'request' => $request,
            'metadata' => $previewData['metadata'],
            'title' => $previewData['title'],
            'previewData' => $previewData,
        ]);
    }

    /**
     * Confirm and persist the custom exam.
     */
    public function confirm(Request $request, AcademicSubject $academicSubject): ?RedirectResponse
    {
        try {
            // Gate: user must have an active subscription covering this subject
            $subscription = $this->ensureActiveSubscription($academicSubject);

            $previewData = session('custom_exam_preview');

            if (! $previewData) {
                return redirect()->route('mock-exams.generate.custom.configure', [
                    'academicSubject' => $academicSubject,
                ])
                    ->withErrors(['general' => 'No preview data found. Please configure your exam first.']);
            }

            $validatedData = $request->all();
            $validatedData['team_id'] = $previewData['team_id'];
            $validatedData['creator_id'] = $previewData['creator_id'];

            // Ensure title is a string (Livewire wire:model on named fields can cause duplicate POST values → array)
            $examTitle = $previewData['title'] ?? 'Custom Exam';
            $examTitle = is_array($examTitle) ? (is_string($examTitle[0] ?? '') ? $examTitle[0] : implode(' ', array_filter($examTitle, 'is_string'))) : (string) $examTitle;

            // Same for instructions which may come from rich-editor as [down, up]
            $examInstructions = $previewData['heading']['instructions'] ?? null;
            $examInstructions = is_array($examInstructions) ? ($examInstructions['down'] ?? $examInstructions[0] ?? null) : $examInstructions;

            // Create the mock exam container
            $exam = $this->creationService->createExam((int) auth()->id(), [
                'title' => $examTitle,
                'description' => null,
                'instructions' => $examInstructions,
                'status' => 'published',
                'delivery_type' => 'print',
                'source_type' => 'custom',
                'academic_subject_id' => $academicSubject->id,
                'mock_exam_subscription_id' => $subscription->id,
            ]);

            // Build the subject exam payload from the preview data
            $sectionsPayload = [];
            foreach ($previewData['sections'] as $section) {
                $sectionsPayload[] = [
                    'title' => $section['name'] ?? '',
                    'instructions' => $section['instructions'] ?? null,
                    'question_type' => str_replace('_questions', '', $section['type'] ?? 'multiple_choice_questions'),
                    'question_count' => count($section['questions'] ?? []),
                    'marks_per_question' => 1,
                    'is_randomized' => false,
                    'topic_ids' => $section['topic_ids'] ?? [],
                    'subtopic_ids' => $section['subtopic_ids'] ?? [],
                    'insert_blank_page' => $section['page'] === 'blank-page',
                ];
            }

            // Normalize duration which may also be an array from Livewire
            $examDuration = $previewData['heading']['duration'] ?? 0;
            $examDuration = is_array($examDuration) ? (int) ($examDuration[0] ?? 0) : (int) $examDuration;

            $subjectExamPayload = [
                'academic_group_id' => $academicSubject->academicLevel->academic_group_id,
                'academic_level_id' => $academicSubject->academic_level_id,
                'academic_subject_id' => $academicSubject->id,
                'title' => $academicSubject->name,
                'instructions' => $examInstructions,
                'duration_in_minutes' => $examDuration,
                'topic_ids' => [],
                'subtopic_ids' => [],
                'sections' => $sectionsPayload,
            ];

            $result = $this->creationService->createSubjectExam($exam, $subjectExamPayload);

            // Increment subscription exam usage
            $subscription->incrementExamUsage();

            // Clear preview data from session
            session()->forget('custom_exam_preview');

            $message = "Your custom exam is ready — {$result['questions_created']} question(s) loaded.";
            if (! empty($result['warnings'])) {
                $message .= ' Note: '.implode(' ', $result['warnings']);
            }

            return redirect()
                ->route('mock-exams.pdf', $exam)
                ->with('success', $message);

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Custom exam confirmation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['general' => 'Failed to create exam. Please try again.', 'error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Verify the user has an active subscription covering the given subject with available exam slots.
     */
    private function ensureActiveSubscription(AcademicSubject $academicSubject): MockExamSubscription
    {
        $subscription = MockExamSubscription::where('user_id', auth()->id())
            ->active()
            ->whereHas('subjects', fn ($q) => $q->where('academic_subjects.id', $academicSubject->id))
            ->get()
            ->first(fn (MockExamSubscription $sub) => $sub->canCreateExam());

        abort_unless(
            $subscription,
            403,
            'You need an active mock exam subscription covering this subject, with exam slots remaining, to generate a custom exam.'
        );

        return $subscription;
    }

    /**
     * Format sections for preview display, loading full question objects.
     */
    private function formatSections(array $sections): array
    {
        $questionGenerator = new QuestionGenerator;

        return collect($sections)->map(function ($section) use ($questionGenerator) {
            if (! empty($section['questions'])) {
                $questionType = str_replace('_questions', '', $section['type']);
                $questionIds = $questionGenerator->getQuestionIdsOnly($section['questions']);
                $section['questions'] = $questionGenerator->fetchCompleteQuestions($questionIds, $questionType);
                $section['questions'] = $questionGenerator->formatExistingQuestions($section['questions']);
            }

            return $section;
        })->toArray();
    }
}
