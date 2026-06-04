<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Contracts\ExamCreationServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Services\ExamQuestionPersistenceService;
use App\ExaminationHub\Services\ExamQuestionPreviewService;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExamCreationController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(
        private readonly ExamCreationServiceInterface $creationService,
        private readonly ExamQuestionPreviewService $previewService,
        private readonly ExamQuestionPersistenceService $persistenceService
    ) {}

    public function create(): View
    {
        $seed = request()->query('draft');
        $formData = null;
        if (is_string($seed) && $seed !== '') {
            $decoded = json_decode(base64_decode($seed, true), true);
            if (is_array($decoded)) {
                $formData = $decoded;
            }
        }

        return view('examination-hub.exams.create', [
            'formData' => $formData,
            'hierarchyTree' => $this->hierarchyTree(),
            'editingExam' => null,
        ]);
    }

    public function edit(GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);

        // Show warning if exam has started or has submissions
        $hasStarted = $exam->starts_at && now()->gte($exam->starts_at);
        $hasSubmissions = $exam->submissions()->exists();

        if ($hasStarted || $hasSubmissions) {
            session()->flash('warning', 'Warning: This exam has '.
                ($hasStarted ? 'already started' : '').
                ($hasStarted && $hasSubmissions ? ' and ' : '').
                ($hasSubmissions ? 'existing submissions' : '').
                '. Editing may affect participants or invalidate existing results.');
        }

        $exam->load('sections');
        $formData = [
            'exam_id' => $exam->id,
            'title' => $exam->title,
            'description' => $exam->description,
            'instructions' => $exam->instructions,
            'duration_in_minutes' => $exam->duration_in_minutes,
            'is_randomized' => $exam->is_randomized,
            'starts_at' => optional($exam->starts_at)?->format('Y-m-d\TH:i'),
            'ends_at' => optional($exam->ends_at)?->format('Y-m-d\TH:i'),
            'status' => $exam->status,
            'participant_mode' => $exam->participant_mode,
            'participant_required_fields' => $exam->participant_required_fields ?? ['name', 'email'],
            'configured_match_mode' => $exam->configured_match_mode ?? 'any',
            'academic_group_id' => $exam->sections->first()?->academic_group_id,
            'academic_level_id' => $exam->sections->first()?->academic_level_id,
            'academic_subject_id' => $exam->academic_subject_id,
            'sections' => $exam->sections->map(fn ($section) => [
                'id' => $section->id, // Include section ID for editing
                'title' => $section->title,
                'description' => $section->description,
                'instructions' => $section->instructions,
                'time_limit_minutes' => $section->time_limit_minutes,
                'source_type' => $section->source_type,
                'question_type' => $section->question_type,
                'question_count' => $section->question_count,
                'is_randomized' => $section->is_randomized,
                'topic_ids' => $section->topic_ids ?? [],
                'subtopic_ids' => $section->subtopic_ids ?? [],
            ])->values()->all(),
        ];

        return view('examination-hub.exams.create', [
            'formData' => $formData,
            'hierarchyTree' => $this->hierarchyTree(),
            'editingExam' => $exam,
        ]);
    }

    public function preview(Request $request): View
    {
        Log::info('Preview request received', [
            'all_input' => $request->all(),
            'sections_count' => count($request->input('sections', [])),
            'sections' => $request->input('sections'),
        ]);

        $payload = $this->validatedPayload($request);

        Log::info('Validated payload', [
            'sections_count' => count($payload['sections']),
            'sections' => $payload['sections'],
        ]);

        $hardenedMode = (bool) ($payload['hardened_mode'] ?? false);
        $examId = $payload['exam_id'] ?? null;

        // If editing an existing exam, load existing questions instead of generating new ones
        if ($examId) {
            $exam = GeneralExam::with(['sections.questions'])->findOrFail((int) $examId);
            $this->ensureOwnerAccess($exam);

            $generatedQuestions = [];
            foreach ($exam->sections as $sectionIndex => $section) {
                $generatedQuestions[$sectionIndex] = $section->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question ?? '',
                        'type' => $question->type ?? 'multiple_choice',
                        'marks' => $question->marks ?? 1,
                        'options' => $this->flattenOptionsToStrings($question->options ?? []),
                        'correct_answer' => $question->correct_answer,
                        'explanation' => $question->explanation,
                        'difficulty' => $question->difficulty ?? 'medium',
                    ];
                })->values()->all();
            }

            Log::info('Loaded existing questions for editing', [
                'exam_id' => $examId,
                'sections_count' => count($generatedQuestions),
            ]);
        } else {
            // Generate new questions for new exam
            $generatedQuestions = $this->previewService->generateForSections(
                $payload['sections'],
                $hardenedMode
            );

            Log::info('Preview generated questions', [
                'hardened_mode' => $hardenedMode,
                'sections_count' => count($payload['sections']),
                'generated_questions_count' => count($generatedQuestions),
                'generated_questions' => $generatedQuestions,
            ]);
        }

        return view('examination-hub.exams.preview', [
            'payload' => $payload,
            'payloadJson' => base64_encode(json_encode($payload, JSON_THROW_ON_ERROR)),
            'generatedQuestions' => $generatedQuestions,
            'hardenedMode' => $hardenedMode,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $encoded = (string) $request->input('payload_json');
        abort_if($encoded === '', 422, 'Missing preview payload.');

        $payload = json_decode(base64_decode($encoded, true), true, 512, JSON_THROW_ON_ERROR);
        $questionsJson = (string) $request->input('questions_json', '');
        $hardenedMode = (bool) ($payload['hardened_mode'] ?? false);

        Log::info('Store exam - questions_json received', [
            'questions_json_length' => strlen($questionsJson),
            'questions_json_empty' => empty($questionsJson),
            'hardened_mode' => $hardenedMode,
        ]);

        $questionsData = ! empty($questionsJson) ? json_decode(base64_decode($questionsJson, true), true, 512, JSON_THROW_ON_ERROR) : [];

        Log::info('Store exam - decoded questions', [
            'questions_data_count' => count($questionsData ?? []),
            'questions_data' => $questionsData,
        ]);

        $examId = $payload['exam_id'] ?? null;
        if ($examId) {
            $exam = GeneralExam::findOrFail((int) $examId);
            $this->ensureOwnerAccess($exam);

            // Allow editing but log warning if exam has started or has submissions
            $hasStarted = $exam->starts_at && now()->gte($exam->starts_at);
            $hasSubmissions = $exam->submissions()->exists();

            if ($hasStarted || $hasSubmissions) {
                Log::warning('Exam edited after start or with submissions', [
                    'exam_id' => $exam->id,
                    'has_started' => $hasStarted,
                    'has_submissions' => $hasSubmissions,
                    'user_id' => auth()->id(),
                ]);
            }

            $exam = $this->creationService->updateExam($exam, (int) auth()->id(), $payload);
        } else {
            $exam = $this->creationService->createExam((int) auth()->id(), $payload);
        }

        // If hardened mode, generate questions now (not during preview)
        if ($hardenedMode) {
            Log::info('Hardened mode: generating questions now', ['exam_id' => $exam->id]);
            $questionsData = $this->previewService->generateForSections($payload['sections'], false);
            Log::info('Hardened mode: questions generated', ['count' => count($questionsData)]);
        }

        if (! empty($questionsData)) {
            Log::info('Persisting questions for exam', ['exam_id' => $exam->id]);
            $this->persistenceService->persistQuestionsForExam($exam, $questionsData);
        } else {
            Log::warning('No questions data to persist', ['exam_id' => $exam->id]);
        }

        return redirect()
            ->route('examination-hub.exams.show', $exam)
            ->with('success', $examId ? 'Examination updated successfully.' : 'Examination created successfully.');
    }

    private function validatedPayload(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'exam_id' => ['nullable', 'integer', 'exists:general_exams,id'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'duration_in_minutes' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'is_randomized' => ['nullable', 'boolean'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', 'in:draft,published'],
            'hardened_mode' => ['nullable', 'boolean'],
            'participant_mode' => ['required', 'in:general,configured,both'],
            'participant_required_fields' => ['required', 'array', 'min:1'],
            'participant_required_fields.*' => ['in:name,email,code'],
            'configured_match_mode' => ['required', 'in:any,both'],
            'academic_group_id' => ['nullable', 'integer', 'exists:academic_groups,id'],
            'academic_level_id' => ['nullable', 'integer', 'exists:academic_levels,id'],
            'academic_subject_id' => ['nullable', 'integer', 'exists:academic_subjects,id'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'integer', 'exists:general_exam_sections,id'],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.description' => ['nullable', 'string'],
            'sections.*.instructions' => ['nullable', 'string'],
            'sections.*.time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'sections.*.source_type' => ['required', 'in:database,ai,mixed,manual'],
            'sections.*.question_type' => ['required', 'in:multiple_choice,true_false,short_answer,essay,mixed'],
            'sections.*.question_count' => ['nullable', 'integer', 'min:1', 'max:500'],
            'sections.*.database_count' => ['nullable', 'integer', 'min:0', 'max:500'],
            'sections.*.ai_count' => ['nullable', 'integer', 'min:0', 'max:500'],
            'sections.*.manual_count' => ['nullable', 'integer', 'min:0', 'max:500'],
            'sections.*.topic_ids' => ['nullable', 'array'],
            'sections.*.topic_ids.*' => ['integer', 'exists:academic_topics,id'],
            'sections.*.subtopic_ids' => ['nullable', 'array'],
            'sections.*.subtopic_ids.*' => ['integer', 'exists:academic_subtopics,id'],
            'sections.*.is_randomized' => ['nullable', 'boolean'],
            'sections.*.document_path' => ['nullable', 'string'],
            'sections.*.document_name' => ['nullable', 'string'],
            'sections.*.has_document' => ['nullable', 'boolean'],
        ]);

        // Validate academic hierarchy if provided
        if (! empty($data['academic_group_id']) || ! empty($data['academic_level_id']) || ! empty($data['academic_subject_id'])) {
            $groupId = (int) ($data['academic_group_id'] ?? 0);
            $levelId = (int) ($data['academic_level_id'] ?? 0);
            $subjectId = (int) ($data['academic_subject_id'] ?? 0);

            if ($levelId && $groupId) {
                $level = AcademicLevel::find($levelId);
                if (! $level || (int) $level->academic_group_id !== $groupId) {
                    throw ValidationException::withMessages([
                        'academic_level_id' => 'Selected level does not belong to selected group.',
                    ]);
                }
            }

            if ($subjectId && $levelId) {
                $subject = AcademicSubject::find($subjectId);
                if (! $subject || (int) $subject->academic_level_id !== $levelId) {
                    throw ValidationException::withMessages([
                        'academic_subject_id' => 'Selected subject does not belong to selected level.',
                    ]);
                }
            }
        }

        foreach ($data['sections'] as $idx => $section) {
            $sourceType = $section['source_type'] ?? '';
            $needsHierarchy = in_array($sourceType, ['database', 'mixed'], true);

            if ($needsHierarchy && (empty($data['academic_group_id']) || empty($data['academic_level_id']) || empty($data['academic_subject_id']))) {
                throw ValidationException::withMessages([
                    'academic_subject_id' => 'Section '.($idx + 1).' requires exam-level academic hierarchy (group, level, subject) for database/mixed source.',
                ]);
            }

            // Copy exam-level hierarchy to sections for database/mixed sources
            if ($needsHierarchy) {
                $data['sections'][$idx]['academic_group_id'] = $data['academic_group_id'];
                $data['sections'][$idx]['academic_level_id'] = $data['academic_level_id'];
                $data['sections'][$idx]['academic_subject_id'] = $data['academic_subject_id'];
            }

            if ($sourceType === 'mixed') {
                $dbCount = (int) ($section['database_count'] ?? 0);
                $aiCount = (int) ($section['ai_count'] ?? 0);
                $manualCount = (int) ($section['manual_count'] ?? 0);
                $data['sections'][$idx]['question_count'] = $dbCount + $aiCount + $manualCount;
            }
        }

        $required = Arr::wrap($data['participant_required_fields'] ?? []);
        $mode = $data['participant_mode'];
        if ($mode === 'general' && in_array('code', $required, true)) {
            $data['participant_required_fields'] = array_values(array_unique(array_filter($required, fn ($f) => $f !== 'code')));
        }

        return $data;
    }

    private function hierarchyTree(): array
    {
        return AcademicGroup::query()
            ->with(['academicLevels.academicSubjects.topics.subtopics'])
            ->orderBy('name')
            ->get()
            ->map(fn ($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'levels' => $group->academicLevels->map(fn ($level) => [
                    'id' => $level->id,
                    'name' => $level->name,
                    'subjects' => $level->academicSubjects->map(fn ($subject) => [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'topics' => $subject->topics->map(fn ($topic) => [
                            'id' => $topic->id,
                            'name' => $topic->name,
                            'subtopics' => $topic->subtopics->map(fn ($subtopic) => [
                                'id' => $subtopic->id,
                                'name' => $subtopic->name,
                            ])->values()->all(),
                        ])->values()->all(),
                    ])->values()->all(),
                ])->values()->all(),
            ])->values()->all();
    }

    /**
     * Flatten deeply nested options to simple key-value pairs.
     * Options can become nested during multiple edits, this fixes that.
     */
    private function flattenOptions(array $options): array
    {
        if (empty($options)) {
            return [];
        }

        return array_map(function ($option) {
            // If option value is already a simple string, keep it
            if (is_string($option['value'] ?? null)) {
                return [
                    'key' => $option['key'] ?? '',
                    'value' => $option['value'],
                ];
            }

            // If option value is nested (has 'value' key), extract the deepest value
            if (is_array($option['value'] ?? null)) {
                $deepestValue = $option['value'];
                while (is_array($deepestValue) && isset($deepestValue['value'])) {
                    $deepestValue = $deepestValue['value'];
                }
                return [
                    'key' => $option['key'] ?? '',
                    'value' => is_string($deepestValue) ? $deepestValue : '',
                ];
            }

            return $option;
        }, $options);
    }

    /**
     * Convert options to simple strings for the question editor.
     * The editor expects simple string options, not key/value arrays.
     */
    private function flattenOptionsToStrings(array $options): array
    {
        if (empty($options)) {
            return [];
        }

        return array_map(function ($option) {
            // If already a string, return as-is
            if (is_string($option)) {
                return $option;
            }

            // If it's an array with key/value, extract the value
            if (is_array($option)) {
                $value = $option['value'] ?? '';
                
                // Handle nested values
                while (is_array($value) && isset($value['value'])) {
                    $value = $value['value'];
                }
                
                return is_string($value) ? $value : '';
            }

            return '';
        }, $options);
    }
}
