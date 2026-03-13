<?php

namespace App\Livewire\Shared;

use App\Models\AcademicSubject;
use App\Models\Assignment;
use App\Models\AssignmentSection;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Services\AcademicChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class QuizCreator extends Component
{
    use WithFileUploads;

    public $title = '';

    public $description = '';

    public $instructions = '';

    public $quizDuration = 0;

    public $starts_at;

    public $ends_at;

    public $is_randomized = false;

    public $restrict_navigation = false;

    public $max_tab_switches = 0;

    public $auto_submit_on_violation = true;

    public $subject_id;

    public $sections = [];

    // UI State
    public $activeSectionIndex = 0;

    public $showQuestionModal = false;

    public $searchQuery = '';

    public $dbQuestions = [];

    public $dbQuestionCount = 5;

    public $dbSelectionMode = 'manual_select'; // manual_select, random

    public $sourcingMode = 'manual'; // manual, db, ai

    // Database Sourcing Filters
    public $dbGroupId;

    public $dbLevelId;

    public $dbSubjectId;

    public $dbTopicId;

    public $dbSubtopicId;

    public $dbQuestionType = 'all'; // all, multiple_choice, true_false, essay

    public $groups = [];

    public $levels = [];

    public $subjects_list = [];

    public $topics = [];

    public $subtopics = [];

    // AI Sourcing State
    public $aiMode = 'prompt'; // prompt, document

    public $aiPrompt = '';

    public $aiDocument;

    public $aiMcqCount = 5;

    public $aiTfCount = 0;

    public $aiEssayCount = 0;

    public $aiGenerating = false;

    // Manual Question State
    public $manualQuestion = [
        'text' => '',
        'type' => 'multiple_choice',
        'options' => ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''],
        'correct_answer' => '',
        'points' => 1,
    ];

    // Audience Configuration
    public $audienceType = 'students'; // students, guests, public

    public $selectedGroups = [];

    public $selectedLevels = [];

    public $selectedStudents = [];

    public $studentSearch = '';

    public $foundStudents = [];

    public function mount()
    {
        $this->addSection();
        $this->groups = \App\Models\AcademicGroup::all();
        $this->levels = \App\Models\AcademicLevel::all();
        $this->starts_at = now()->format('Y-m-d\TH:i');
        $this->ends_at = now()->addDays(7)->format('Y-m-d\TH:i');
    }

    public function updatedStudentSearch($value)
    {
        if (strlen($value) < 2) {
            $this->foundStudents = [];

            return;
        }

        $this->foundStudents = \App\Models\Student::with('user')
            ->whereHas('user', function ($q) use ($value) {
                $q->where('name', 'like', '%'.$value.'%')
                    ->orWhere('email', 'like', '%'.$value.'%');
            })
            ->orWhere('student_id', 'like', '%'.$value.'%')
            ->limit(10)
            ->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => ($s->user->name ?? 'N/A').' ('.$s->student_id.')'])
            ->toArray();
    }

    public function updatedSubjectId($id)
    {
        if ($id) {
            $subject = AcademicSubject::with('academicLevel.academicGroup')->find($id);
            if ($subject && $subject->academicLevel) {
                $this->dbGroupId = $subject->academicLevel->academicGroup->id;
                $this->updatedDbGroupId($this->dbGroupId);

                $this->dbLevelId = $subject->academicLevel->id;
                $this->updatedDbLevelId($this->dbLevelId);

                $this->dbSubjectId = $subject->id;
                $this->updatedDbSubjectId($this->dbSubjectId);
            }
        }
    }

    #[On('update-subject_id')]
    public function handleSubjectIdUpdate($id): void
    {
        $this->subject_id = $id;
        $this->updatedSubjectId($id);
    }

    #[On('update-manualQuestionType')]
    public function handleManualQuestionTypeUpdate($type): void
    {
        $this->manualQuestion['type'] = $type;
        $this->updatedManualQuestionType($type);
    }

    #[On('update-manualQuestionCorrectAnswer')]
    public function handleManualQuestionCorrectAnswerUpdate($answer): void
    {
        $this->manualQuestion['correct_answer'] = $answer;
    }

    #[On('update-dbGroupId')]
    public function handleDbGroupIdUpdate($id): void
    {
        $this->dbGroupId = $id;
        $this->updatedDbGroupId($id);
    }

    #[On('update-dbLevelId')]
    public function handleDbLevelIdUpdate($id): void
    {
        $this->dbLevelId = $id;
        $this->updatedDbLevelId($id);
    }

    #[On('update-dbSubjectId')]
    public function handleDbSubjectIdUpdate($id): void
    {
        $this->dbSubjectId = $id;
        $this->updatedDbSubjectId($id);
    }

    #[On('update-dbTopicId')]
    public function handleDbTopicIdUpdate($id): void
    {
        $this->dbTopicId = $id;
        $this->updatedDbTopicId($id);
    }

    #[On('update-dbSubtopicId')]
    public function handleDbSubtopicIdUpdate($id): void
    {
        $this->dbSubtopicId = $id;
        $this->updatedDbSubtopicId($id);
    }

    #[On('update-dbQuestionType')]
    public function handleDbQuestionTypeUpdate($type): void
    {
        $this->dbQuestionType = $type;
        $this->searchQuestions();
    }

    #[On('update-dbSelectionMode')]
    public function handleDbSelectionModeUpdate($mode): void
    {
        $this->dbSelectionMode = $mode;
        $this->updatedDbSelectionMode($mode);
    }

    #[On('update-audienceType')]
    public function handleAudienceTypeUpdate($type): void
    {
        $this->audienceType = $type;
    }

    #[On('update-selectedGroups')]
    public function handleSelectedGroupsUpdate($ids): void
    {
        $this->selectedGroups = is_array($ids) ? $ids : [$ids];
    }

    #[On('update-selectedLevels')]
    public function handleSelectedLevelsUpdate($ids): void
    {
        $this->selectedLevels = is_array($ids) ? $ids : [$ids];
    }

    #[On('update-selectedStudents')]
    public function handleSelectedStudentsUpdate($ids): void
    {
        $this->selectedStudents = is_array($ids) ? $ids : [$ids];
    }

    public function updatedManualQuestionType($type)
    {
        $this->manualQuestion['correct_answer'] = '';
        if ($type === 'multiple_choice') {
            $this->manualQuestion['options'] = ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''];
        } elseif ($type === 'true_false') {
            $this->manualQuestion['options'] = ['True' => 'True', 'False' => 'False'];
        } else {
            $this->manualQuestion['options'] = [];
        }
    }

    public function updatedDbGroupId($id)
    {
        $this->dbGroupId = $id;
        $this->levels = $id ? \App\Models\AcademicLevel::where('academic_group_id', $id)->get() : [];
        $this->reset(['dbLevelId', 'dbSubjectId', 'dbTopicId', 'dbSubtopicId', 'subjects_list', 'topics', 'subtopics']);
    }

    public function updatedDbLevelId($id)
    {
        $this->dbLevelId = $id;
        $this->subjects_list = $id ? \App\Models\AcademicSubject::where('academic_level_id', $id)->get() : [];
        $this->reset(['dbSubjectId', 'dbTopicId', 'dbSubtopicId', 'topics', 'subtopics']);
    }

    public function updatedDbSubjectId($id)
    {
        $this->dbSubjectId = $id;
        $this->topics = $id ? \App\Models\AcademicTopic::where('academic_subject_id', $id)->get() : [];
        $this->reset(['dbTopicId', 'dbSubtopicId', 'subtopics']);
        $this->searchQuestions();
    }

    public function updatedDbTopicId($id)
    {
        $this->dbTopicId = $id;
        $this->subtopics = $id ? \App\Models\AcademicSubtopic::where('academic_topic_id', $id)->get() : [];
        $this->reset(['dbSubtopicId']);
        $this->searchQuestions();
    }

    public function updatedDbSubtopicId($id)
    {
        $this->dbSubtopicId = $id;
        $this->searchQuestions();
    }

    public function updatedDbQuestionCount($value)
    {
        if ($this->dbSelectionMode === 'random') {
            $this->searchQuestions();
        }
    }

    public function updatedDbSelectionMode($value)
    {
        $this->searchQuestions();
    }

    public function addSection()
    {
        $this->sections[] = [
            'id' => uniqid(),
            'title' => 'New Section '.(count($this->sections) + 1),
            'instructions' => '',
            'duration_minutes' => 0,
            'grading_mode' => 'automatic',
            'questions' => [],
        ];
        $this->activeSectionIndex = count($this->sections) - 1;
    }

    public function removeSection($index)
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
        $this->activeSectionIndex = max(0, $this->activeSectionIndex - 1);
    }

    public function setSourcingMode($mode)
    {
        $this->sourcingMode = $mode;
        if ($mode === 'db') {
            $this->searchQuestions();
        }
    }

    public function searchQuestions()
    {
        if (! $this->dbSubjectId) {
            $this->dbQuestions = [];

            return;
        }

        $query_mcqs = MultipleChoiceQuestion::query()
            ->when($this->dbQuestionType !== 'all' && $this->dbQuestionType !== 'multiple_choice', fn ($q) => $q->whereRaw('1=0'))
            ->when($this->dbSubjectId, function ($q) {
                $q->whereHas('academicTopic', fn ($t) => $t->where('academic_subject_id', $this->dbSubjectId));
            })
            ->when($this->dbTopicId, fn ($q) => $q->where('academic_topic_id', $this->dbTopicId))
            ->when($this->dbSubtopicId, fn ($q) => $q->where('academic_subtopic_id', $this->dbSubtopicId))
            ->when($this->searchQuery && $this->dbSelectionMode === 'manual_select', fn ($q) => $q->where('question', 'like', "%{$this->searchQuery}%"));

        $query_tfqs = TrueOrFalseQuestion::query()
            ->when($this->dbQuestionType !== 'all' && $this->dbQuestionType !== 'true_false', fn ($q) => $q->whereRaw('1=0'))
            ->when($this->dbSubjectId, function ($q) {
                $q->whereHas('academicTopic', fn ($t) => $t->where('academic_subject_id', $this->dbSubjectId));
            })
            ->when($this->dbTopicId, fn ($q) => $q->where('academic_topic_id', $this->dbTopicId))
            ->when($this->dbSubtopicId, fn ($q) => $q->where('academic_subtopic_id', $this->dbSubtopicId))
            ->when($this->searchQuery && $this->dbSelectionMode === 'manual_select', fn ($q) => $q->where('question', 'like', "%{$this->searchQuery}%"));

        $query_essays = EssayQuestion::query()
            ->when($this->dbQuestionType !== 'all' && $this->dbQuestionType !== 'essay', fn ($q) => $q->whereRaw('1=0'))
            ->when($this->dbSubjectId, function ($q) {
                $q->whereHas('academicTopic', fn ($t) => $t->where('academic_subject_id', $this->dbSubjectId));
            })
            ->when($this->dbTopicId, fn ($q) => $q->where('academic_topic_id', $this->dbTopicId))
            ->when($this->dbSubtopicId, fn ($q) => $q->where('academic_subtopic_id', $this->dbSubtopicId))
            ->when($this->searchQuery && $this->dbSelectionMode === 'manual_select', fn ($q) => $q->where('question', 'like', "%{$this->searchQuery}%"));

        if ($this->dbSelectionMode === 'random') {
            $total_to_get = max(1, (int) $this->dbQuestionCount);

            // Fetch a few of each to make it balanced or just a pool
            $mcqs = $query_mcqs->inRandomOrder()->limit($total_to_get)->get()->map(fn ($q) => $this->formatDbQuestion($q, 'multiple_choice'));
            $tfqs = $query_tfqs->inRandomOrder()->limit($total_to_get)->get()->map(fn ($q) => $this->formatDbQuestion($q, 'true_false'));
            $essays = $query_essays->inRandomOrder()->limit($total_to_get)->get()->map(fn ($q) => $this->formatDbQuestion($q, 'essay'));

            $pool = $mcqs->concat($tfqs)->concat($essays)->shuffle();
            $this->dbQuestions = $pool->take($total_to_get)->toArray();
        } else {
            $mcqs = $query_mcqs->limit(20)->get()->map(fn ($q) => $this->formatDbQuestion($q, 'multiple_choice'));
            $tfqs = $query_tfqs->limit(20)->get()->map(fn ($q) => $this->formatDbQuestion($q, 'true_false'));
            $essays = $query_essays->limit(20)->get()->map(fn ($q) => $this->formatDbQuestion($q, 'essay'));

            $this->dbQuestions = $mcqs->concat($tfqs)->concat($essays)->toArray();
        }
    }

    public function addAllQuestions()
    {
        foreach ($this->dbQuestions as $q) {
            $this->addQuestionToSection($q);
        }
        $this->showQuestionModal = false;
    }

    protected function formatDbQuestion($q, $type)
    {
        // Extract text from Mark object or array
        $text = $this->extractText($q->question);

        $data = [
            'id' => $q->id,
            'text' => $text,
            'type' => $type,
            'points' => $q->score ?? 1,
        ];

        if ($type === 'multiple_choice') {
            $data['options'] = [
                'A' => $this->extractText($q->option_a),
                'B' => $this->extractText($q->option_b),
                'C' => $this->extractText($q->option_c),
                'D' => $this->extractText($q->option_d),
                'E' => $this->extractText($q->option_e),
            ];
            $data['correct_answer'] = $q->answer;
        } elseif ($type === 'true_false') {
            $data['correct_answer'] = $q->answer ? 'True' : 'False';
        } elseif ($type === 'essay') {
            $data['correct_answer'] = $this->extractText($q->answer);
        }

        return $data;
    }

    protected function extractText($field)
    {
        if (is_object($field)) {
            return $field->text ?? $field->down ?? '';
        }
        if (is_array($field)) {
            return $field['text'] ?? $field['down'] ?? '';
        }

        return (string) $field;
    }

    public function addQuestionToSection($questionData)
    {
        // Check for duplicates in the current section
        $exists = collect($this->sections[$this->activeSectionIndex]['questions'])->contains(function ($q) use ($questionData) {
            // If it has an ID, check by ID and type (for DB questions)
            if (isset($q['id'], $questionData['id']) && $q['id'] == $questionData['id'] && $q['type'] == $questionData['type']) {
                return true;
            }

            // Otherwise check by text content
            return trim($q['text']) === trim($questionData['text']);
        });

        if (! $exists) {
            $this->sections[$this->activeSectionIndex]['questions'][] = $questionData;
        }

        $this->showQuestionModal = false;
    }

    public function addManualQuestion()
    {
        $this->validate([
            'manualQuestion.text' => 'required',
            'manualQuestion.type' => 'required',
            'manualQuestion.correct_answer' => 'required_unless:manualQuestion.type,essay',
        ], [], [
            'manualQuestion.text' => 'question text',
            'manualQuestion.type' => 'question type',
            'manualQuestion.correct_answer' => 'correct answer',
        ]);

        $this->addQuestionToSection($this->manualQuestion);
        $this->reset('manualQuestion');
        $this->manualQuestion = [
            'text' => '',
            'type' => 'multiple_choice',
            'options' => ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''],
            'correct_answer' => '',
            'points' => 1,
        ];
    }

    public function generateWithAi()
    {
        $totalQuestions = (int) $this->aiMcqCount + (int) $this->aiTfCount + (int) $this->aiEssayCount;

        if ($totalQuestions <= 0) {
            session()->flash('error', 'Please specify the number of questions to generate.');

            return;
        }

        if ($totalQuestions > 30) {
            session()->flash('error', 'You can generate up to 30 questions at once.');

            return;
        }

        if ($this->aiMode === 'prompt') {
            $this->validate(['aiPrompt' => 'required']);
        } else {
            $this->validate(['aiDocument' => 'required|mimes:pdf,doc,docx,txt|max:10240']);
        }

        $this->aiGenerating = true;

        try {
            $chatService = app(AcademicChatService::class);

            if ($this->aiMode === 'prompt') {
                $context = "Prompt: {$this->aiPrompt}";
            } else {
                // Extract text from document
                $extractedText = $chatService->extractFileContent($this->aiDocument);
                if (empty(trim($extractedText))) {
                    throw new \Exception('Could not extract any text from the uploaded document.');
                }
                $context = "Extracted Content from {$this->aiDocument->getClientOriginalName()}:\n\n{$extractedText}";
            }

            $typesPrompt = [];
            if ($this->aiMcqCount > 0) {
                $typesPrompt[] = "{$this->aiMcqCount} multiple_choice questions (with A-E options)";
            }
            if ($this->aiTfCount > 0) {
                $typesPrompt[] = "{$this->aiTfCount} true_false questions";
            }
            if ($this->aiEssayCount > 0) {
                $typesPrompt[] = "{$this->aiEssayCount} essay (short answer) questions";
            }

            $typesString = implode(', ', $typesPrompt);

            $prompt = "Generate {$totalQuestions} educational questions based on this content:
            {$context}

            The questions should be distributed as follows: {$typesString}.

            Return the result as a JSON array of objects, each having:
            'text' (the question),
            'type' (one of: multiple_choice, true_false, essay),
            'options' (for multiple_choice, an object with A, B, C, D, E keys; for true_false and essay, use an empty object {}),
            'correct_answer' (string value: for multiple_choice use the key like 'A', for true_false use 'True' or 'False', for essay use a sample model answer),
            'points' (integer).

            Ensure the JSON is valid and ONLY return the JSON array, no extra text.";

            Log::info('Quiz AI Generation Request', ['mode' => $this->aiMode]);

            $response = $chatService->processRequest([
                'input' => $prompt,
                'request_type' => 'quiz_generation',
            ], []);

            $content = $response['content'] ?? $response['answer'] ?? '';

            Log::info('Quiz AI Generation Response', ['content' => $content]);

            if (empty($content) && ! empty($response['error'])) {
                throw new \Exception($response['error']);
            }

            // Clean JSON if needed
            if (preg_match('/\[.*\]/s', $content, $matches)) {
                $questions = json_decode($matches[0], true);
                if ($questions) {
                    $addedCount = 0;
                    foreach ($questions as $q) {
                        // Ensure options is an array/object
                        if (! isset($q['options']) || ! is_array($q['options'])) {
                            $q['options'] = [];
                        }

                        // Check for duplicates before adding AI generated questions
                        $exists = collect($this->sections[$this->activeSectionIndex]['questions'])->contains(function ($existingQ) use ($q) {
                            return trim($existingQ['text']) === trim($q['text']);
                        });

                        if (! $exists) {
                            $this->sections[$this->activeSectionIndex]['questions'][] = $q;
                            $addedCount++;
                        }
                    }

                    if ($addedCount === 0 && count($questions) > 0) {
                        session()->flash('info', 'All generated questions were already in the quiz.');
                    }
                } else {
                    Log::error('Quiz AI JSON Decode Failed', ['content' => $content]);
                    session()->flash('error', 'AI returned an invalid format. Please try again.');
                }
            } else {
                Log::error('Quiz AI No JSON Found', ['content' => $content]);
                session()->flash('error', 'AI did not return any questions in the expected format.');
            }
        } catch (\Exception $e) {
            Log::error('Quiz AI Generation Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'AI Generation failed: '.$e->getMessage());
        }

        $this->aiGenerating = false;
        $this->showQuestionModal = false;
    }

    public function removeQuestion($sectionIndex, $questionIndex)
    {
        unset($this->sections[$sectionIndex]['questions'][$questionIndex]);
        $this->sections[$sectionIndex]['questions'] = array_values($this->sections[$sectionIndex]['questions']);
    }

    public function saveQuiz()
    {
        $this->validate([
            'title' => 'required',
            'subject_id' => 'required',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string|min:1',
        ], [
            'sections.*.title.required' => 'All sections must have a title.',
        ]);

        try {
            DB::transaction(function () {
                $totalMarks = collect($this->sections)->sum(function ($section) {
                    return collect($section['questions'])->sum('points');
                });

                $assignment = Assignment::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'instructions' => $this->instructions,
                    'academic_subject_id' => $this->subject_id,
                    'user_id' => Auth::id(),
                    'type' => 'quiz',
                    'questions' => $this->sections, // Store all sections and their questions in the JSON column
                    'status' => 'published',
                    'duration_in_minutes' => $this->quizDuration > 0 ? $this->quizDuration : collect($this->sections)->sum('duration_minutes'),
                    'starts_at' => $this->starts_at,
                    'ends_at' => $this->ends_at,
                    'is_randomized' => $this->is_randomized,
                    'restrict_navigation' => $this->restrict_navigation,
                    'max_tab_switches' => $this->max_tab_switches,
                    'auto_submit_on_violation' => $this->auto_submit_on_violation,
                    'total_marks' => $totalMarks,
                ]);

                foreach ($this->sections as $index => $section) {
                    AssignmentSection::create([
                        'assignment_id' => $assignment->id,
                        'title' => $section['title'],
                        'instructions' => $section['instructions'],
                        'question_count' => count($section['questions']),
                        'marks_per_question' => 1,
                        'order' => $index,
                        'question_type' => 'mixed', // Sections can have mixed types now
                        'duration_minutes' => $section['duration_minutes'] ?? 0,
                    ]);
                }

                // Attach Audience
                if ($this->audienceType === 'students') {
                    if (! empty($this->selectedGroups)) {
                        $assignment->academicGroups()->sync($this->selectedGroups);
                    }
                    if (! empty($this->selectedLevels)) {
                        $assignment->academicLevels()->sync($this->selectedLevels);
                    }
                    if (! empty($this->selectedStudents)) {
                        $assignment->students()->sync($this->selectedStudents);
                    }
                } elseif ($this->audienceType === 'guests') {
                    // Logic for guests could be specific if there are guest groups or just a flag
                } elseif ($this->audienceType === 'public') {
                    // Public access might be handled by 'status' => 'published' and no restrictions
                }
            });

            session()->flash('message', 'Quiz created successfully!');

            return redirect()->route('teachers.assignments.show', $assignment->id);
        } catch (\Exception $e) {
            Log::error('Quiz Save Error: '.$e->getMessage());
            session()->flash('error', 'Failed to save quiz: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.shared.quiz-creator', [
            'subjects' => AcademicSubject::with(['academicLevel.academicGroup'])->get(),
            'students_list' => \App\Models\User::byRole('student')->get(),
        ]);
    }
}
