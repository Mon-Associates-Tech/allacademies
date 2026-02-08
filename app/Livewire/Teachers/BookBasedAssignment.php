<?php

namespace App\Livewire\Teachers;

use App\Models\AcademicSubject;
use App\Models\Assignment;
use App\Models\Book;
use App\Services\AcademicChatService;
use App\Services\AssignmentNotificationService;
use App\Services\BookBasedLearningService;
use App\Services\PdfContentExtractionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Exception\MissingCatalogException;

class BookBasedAssignment extends Component
{
    use WithFileUploads;

    public $selectedBookId = '';

    public $selectedChapterId = '';

    public $pageStart = '';

    public $pageEnd = '';

    public $questionType = 'mixed';

    public $questionCount = 10;

    public $totalMarks = 10;

    public $difficulty = 'medium';

    public $focusTopics = '';

    public $includeQuotes = false;

    // Assignment properties
    public $title = '';

    public $description = '';

    public $durationInMinutes = 60;

    public $startDate;

    public $endDate;

    public $isRandomized = true;

    public $selectedSubjectId = '';

    // Target groups
    public $selectedStudentGroups = [];

    public $selectedAcademicLevels = [];

    public $selectedAcademicGroups = [];

    public $selectedStudents = [];

    // Component state
    public $availableBooks = [];

    public $availableSubjects = [];

    public $bookChapters = [];

    public $selectedBook = null;

    public $studentGroups = [];

    public $academicLevels = [];

    public $academicGroups = [];

    public $isGenerating = false;

    public $generatedQuestions = [];

    public $uploadedFile = null;

    public $fileContent = '';

    public $fileName = '';

    public $contentSourceTab = 'book';

    // Question management state
    public $editingQuestionIndex = null;

    public $editingQuestion = null;

    public $showDeleteConfirmation = false;

    public $questionToDelete = null;

    public $isRegenerating = false;

    public $regeneratingIndex = null;

    // Wizard step tracking (1 = Setup, 2 = Generate, 3 = Assign)
    public $currentStep = 1;

    protected $rules = [
        'title' => 'required|string|max:255',
        'selectedSubjectId' => 'required|exists:academic_subjects,id',
        'description' => 'nullable|string',
        'selectedBookId' => 'required_without:uploadedFile',
        'questionType' => 'required|in:multiple_choice,true_false,essay,mixed',
        'questionCount' => 'required|integer|min:1|max:50',
        'totalMarks' => 'required|integer|min:1',
        'difficulty' => 'required|in:easy,medium,hard',
        'durationInMinutes' => 'required|integer|min:1',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after:startDate',
        'pageStart' => 'nullable|integer|min:1',
        'pageEnd' => 'nullable|integer|min:1|gte:pageStart',
    ];

    protected $messages = [
        'title.required' => 'Please enter an assignment title.',
        'selectedSubjectId.required' => 'Please select a subject for this assignment.',
        'selectedBookId.required_without' => 'Please select a book or upload a file.',
        'questionCount.min' => 'You must generate at least 1 question.',
        'questionCount.max' => 'You can generate a maximum of 50 questions.',
        'totalMarks.required' => 'Please specify the total marks for this assignment.',
        'totalMarks.min' => 'Total marks must be at least 1.',
        'durationInMinutes.required' => 'Please specify the duration for this assignment.',
        'durationInMinutes.min' => 'Duration must be at least 1 minute.',
        'startDate.required' => 'Please select a start date.',
        'endDate.required' => 'Please select an end date.',
        'endDate.after' => 'End date must be after the start date.',
        'pageEnd.gte' => 'End page must be greater than or equal to start page.',
    ];

    protected BookBasedLearningService $bookLearningService;

    protected AcademicChatService $chatService;

    protected PdfContentExtractionService $pdfExtractor;

    public function boot(
        BookBasedLearningService $bookLearningService,
        AcademicChatService $chatService,
        PdfContentExtractionService $pdfExtractor
    ) {
        $this->bookLearningService = $bookLearningService;
        $this->chatService = $chatService;
        $this->pdfExtractor = $pdfExtractor;
    }

    public function mount()
    {
        $this->loadAvailableBooks();
        $this->loadAvailableSubjects();
        $this->loadTargetGroups();
        $this->startDate = now()->format('Y-m-d H:i');
        $this->endDate = now()->addWeek()->format('Y-m-d H:i');
    }

    protected function loadAvailableBooks()
    {
        $user = Auth::user();

        // Get books from user's subscriptions
        $this->availableBooks = Book::published()
            ->with(['author', 'bookCategory'])
            ->whereHas('subscriptions', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'paid');
                //  ->where(function ($q) {
                //     $q->whereNull('expires_at')
                //        ->orWhere('expires_at', '>', now());
                // });
            })
            ->orderBy('title')
            ->get();
    }

    protected function loadAvailableSubjects()
    {
        // Load subjects the teacher is assigned to or all subjects
        $teacher = Auth::user()->teacher;
        if ($teacher) {
            $this->availableSubjects = $teacher->academicSubjects()->get();
        } else {
            $this->availableSubjects = AcademicSubject::all();
        }
    }

    protected function loadTargetGroups()
    {
        $teacher = Auth::user()->teacher;

        if ($teacher) {
            $this->studentGroups = $teacher->studentGroups()->get();
            $this->academicLevels = $teacher->academicLevels()->get();
            $this->academicGroups = $teacher->academicGroups()->get();
        }
    }

    /**
     * Navigate to the next step in the wizard
     */
    public function nextStep(): void
    {
        // Validate current step before proceeding
        if (! $this->validateCurrentStep()) {
            return;
        }

        if ($this->currentStep < 3) {
            $this->currentStep++;
        }
    }

    /**
     * Navigate to the previous step in the wizard
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Navigate to a specific step in the wizard
     */
    public function goToStep(int $step): void
    {
        // Only allow going back or to completed steps
        if ($step < $this->currentStep) {
            $this->currentStep = $step;

            return;
        }

        // For forward navigation, validate all previous steps
        if ($step > $this->currentStep) {
            // Validate step 1 before going to step 2 or 3
            if ($this->currentStep === 1 && $step >= 2) {
                if (! $this->validateStep1()) {
                    return;
                }
            }

            // Validate step 2 before going to step 3
            if ($step === 3 && ! $this->validateStep2()) {
                return;
            }

            $this->currentStep = $step;
        }
    }

    /**
     * Validate the current step
     */
    protected function validateCurrentStep(): bool
    {
        return match ($this->currentStep) {
            1 => $this->validateStep1(),
            2 => $this->validateStep2(),
            default => true,
        };
    }

    /**
     * Validate Step 1: Setup (title, subject, content source)
     */
    protected function validateStep1(): bool
    {
        $this->resetErrorBag();

        $hasErrors = false;

        if (empty($this->title)) {
            $this->addError('title', 'Please enter an assignment title.');
            $hasErrors = true;
        }

        if (empty($this->selectedSubjectId)) {
            $this->addError('selectedSubjectId', 'Please select a subject for this assignment.');
            $hasErrors = true;
        }

        // Check if content source is selected
        if (empty($this->selectedBookId) && empty($this->fileContent)) {
            $this->addError('selectedBookId', 'Please select a book or upload a file.');
            $hasErrors = true;
        }

        return ! $hasErrors;
    }

    /**
     * Validate Step 2: Generate (questions must be generated)
     */
    protected function validateStep2(): bool
    {
        if (empty($this->generatedQuestions)) {
            $this->addError('questions', 'Please generate questions before proceeding to the next step.');

            return false;
        }

        return true;
    }

    /**
     * Check if Step 1 is complete (for progress indicator)
     */
    public function isStep1Complete(): bool
    {
        return ! empty($this->title)
            && ! empty($this->selectedSubjectId)
            && (! empty($this->selectedBookId) || ! empty($this->fileContent));
    }

    /**
     * Check if Step 2 is complete (for progress indicator)
     */
    public function isStep2Complete(): bool
    {
        return ! empty($this->generatedQuestions);
    }

    public function updatedSelectedBookId()
    {
        if ($this->selectedBookId) {
            $this->selectedBook = Book::with(['author', 'bookCategory'])->find($this->selectedBookId);
            $this->loadBookChapters();
            $this->resetPageRange();

            // Auto-select subject if book has one
            if ($this->selectedBook && $this->selectedBook->subject_id) {
                $this->selectedSubjectId = $this->selectedBook->subject_id;
            }
        } else {
            $this->selectedBook = null;
            $this->bookChapters = [];
        }
    }

    protected function loadBookChapters()
    {
        if (! $this->selectedBook || ! $this->selectedBook->table_of_contents) {
            $this->bookChapters = [];

            return;
        }

        $this->bookChapters = collect($this->selectedBook->formatted_table_of_contents)
            ->map(function ($chapter) {
                return (object) [
                    'id' => $chapter['chapter_number'],
                    'chapter_number' => $chapter['chapter_number'],
                    'title' => $chapter['title'],
                    'page_range' => $chapter['page_range'],
                ];
            });
    }

    protected function resetPageRange()
    {
        $this->pageStart = '';
        $this->pageEnd = '';
    }

    public function updatedUploadedFile()
    {
        $this->validateOnly('uploadedFile');

        if ($this->uploadedFile) {
            try {
                $extension = strtolower($this->uploadedFile->getClientOriginalExtension());

                // Support multiple file types
                if (! in_array($extension, ['pdf', 'doc', 'docx', 'txt'])) {
                    $this->addError('uploadedFile', 'Unsupported file type. Please upload PDF, DOC, DOCX, or TXT files.');

                    return;
                }

                // Extract content from uploaded file using the PDF extraction service
                $this->fileContent = $this->pdfExtractor->extractFromUploadedFile($this->uploadedFile, [
                    'preserve_layout' => false,
                    'method' => 'auto',
                ]);
                $this->fileName = $this->uploadedFile->getClientOriginalName();

                if (empty($this->fileContent)) {
                    $this->addError('uploadedFile', 'Failed to extract content from the uploaded file.');
                }
            } catch (\Exception $e) {
                \Log::error('File content extraction failed', [
                    'user_id' => Auth::id(),
                    'file_name' => $this->uploadedFile->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);

                $this->addError('uploadedFile', 'Unable to extract content from this file. Please ensure it is a valid document file.');
            }
        }
    }

    public function generateQuestions()
    {
        $this->validate([
            'selectedSubjectId' => 'required|exists:academic_subjects,id',
            'questionType' => 'required|in:multiple_choice,true_false,essay,mixed',
            'questionCount' => 'required|integer|min:1|max:50',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        // Check if either a book is selected or a file is uploaded
        if (! $this->selectedBookId && empty($this->fileContent)) {
            $this->addError('selectedBookId', 'Please select a book or upload a file first.');

            return;
        }

        $this->isGenerating = true;

        try {
            $content = '';

            // Extract content based on source
            if ($this->selectedBookId) {
                $content = $this->extractBookContent();
            } else {
                $content = $this->fileContent;
            }

            if (empty($content)) {
                $this->addError('generation', 'Failed to extract content. Please try again.');
                $this->isGenerating = false;

                return;
            }

            $parameters = [
                'book_id' => $this->selectedBookId,
                'chapter_id' => $this->selectedChapterId ?: null,
                'page_start' => $this->pageStart ?: null,
                'page_end' => $this->pageEnd ?: null,
                'question_type' => $this->questionType,
                'question_count' => $this->questionCount,
                'difficulty' => $this->difficulty,
                'focus_topics' => $this->parseFocusTopics(),
                'include_quotes' => $this->includeQuotes,
                'content' => $content,
                'file_content' => $this->fileContent ?: null,
                'file_name' => $this->fileName ?: null,
                'request_type' => 'assignment_generation',
            ];

            // Only include book-related parameters if a book is selected
            if ($this->selectedBook) {
                $parameters = array_merge($parameters, [
                    'book_title' => $this->selectedBook->title,
                    'author' => $this->selectedBook->author_name,
                    'genre' => $this->selectedBook->genre,
                    'themes' => $this->selectedBook->themes ?? [],
                    'difficulty_score' => $this->selectedBook->difficulty_score,
                ]);
            }

            // Generate questions using the book learning service
            $quizData = $this->bookLearningService->generateAdaptiveQuiz(
                Auth::user(),
                $this->selectedBook,
                $parameters
            );

            if (! empty($quizData['questions'])) {
                $this->generatedQuestions = $quizData['questions'];
                $this->dispatch('questions-generated');
            } else {
                $this->addError('generation', 'Failed to generate questions. Please try again with different parameters.');
            }

        } catch (\Exception $e) {
            \Log::error('Question generation failed', [
                'user_id' => Auth::id(),
                'book_id' => $this->selectedBookId,
                'error' => $e->getMessage(),
            ]);

            $this->addError('generation', 'Unable to generate questions. Please try different parameters or try again later.');
        } finally {
            $this->isGenerating = false;
        }
    }

    protected function parseFocusTopics(): array
    {
        if (empty($this->focusTopics)) {
            return [];
        }

        return array_map('trim', explode(',', $this->focusTopics));
    }

    public function createAssignment()
    {
        // NEW: Check school has active content subscription before allowing assignment creation
        $school = auth()->user()->school;
        if (! $school || ! $school->hasActiveContentSubscription()) {
            $this->addError('subscription',
                'Your school must have an active subscription to create assignments. '.
                'Please contact your school administrator.'
            );

            return;
        }

        $this->validate();

        // Validate that questions have been generated
        if (empty($this->generatedQuestions)) {
            $this->addError('questions', 'Please generate questions before creating the assignment.');

            return;
        }

        // Validate that at least one target group is selected
        $hasTargetGroup = ! empty($this->selectedStudentGroups)
            || ! empty($this->selectedAcademicLevels)
            || ! empty($this->selectedAcademicGroups)
            || ! empty($this->selectedStudents);

        if (! $hasTargetGroup) {
            $this->addError('targetGroups', 'Please select at least one target group (student groups, academic levels, academic groups, or individual students).');

            return;
        }

        $teacher = Auth::user()->teacher;

        if (! $teacher) {
            $this->addError('teacher', 'Teacher profile not found.');

            return;
        }

        try {
            DB::beginTransaction();

            $assignment = new Assignment;
            $assignment->teacher_id = $teacher->id;
            $assignment->academic_subject_id = $this->selectedSubjectId;
            $assignment->title = $this->title;
            $assignment->description = $this->description;
            $assignment->type = 'quiz';
            $assignment->duration_in_minutes = $this->durationInMinutes;
            $assignment->starts_at = $this->startDate;
            $assignment->ends_at = $this->endDate;
            $assignment->is_randomized = $this->isRandomized;
            $assignment->status = 'published';
            $assignment->total_marks = $this->totalMarks;
            $assignment->questions = $this->formatQuestionsForAssignment();
            $assignment->save();

            // Attach target groups
            if (! empty($this->selectedStudentGroups)) {
                $assignment->studentGroups()->attach($this->selectedStudentGroups);
            }

            if (! empty($this->selectedAcademicLevels)) {
                $assignment->academicLevels()->attach($this->selectedAcademicLevels);
            }

            if (! empty($this->selectedAcademicGroups)) {
                $assignment->academicGroups()->attach($this->selectedAcademicGroups);
            }

            if (! empty($this->selectedStudents)) {
                $assignment->students()->attach($this->selectedStudents);
            }

            // Log activity
            $assignment->logActivity('create', 'Book-Based Assignment Created', 'assignment', [
                'assignment_title' => $this->title,
                'subject_id' => $this->selectedSubjectId,
                'question_count' => count($this->generatedQuestions),
                'total_marks' => $this->totalMarks,
                'created_by' => auth()->user()?->name ?? 'Unknown',
            ]);

            DB::commit();

            // Send notifications to students
            app(AssignmentNotificationService::class)->sendAssignmentNotifications($assignment);

            $this->dispatch('assignment-created', ['id' => $assignment->id]);
            session()->flash('success', 'Assignment created successfully and notifications sent!');

            return redirect()->route('teachers.assignments.index');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Assignment creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->addError('creation', 'Failed to create assignment. Please try again.');
        }
    }

    protected function formatQuestionsForAssignment(): array
    {
        $questionsConfig = [];

        // Group questions by type
        $questionsByType = collect($this->generatedQuestions)->groupBy('type');

        foreach ($questionsByType as $type => $questions) {
            // Convert questions to proper format
            $formattedQuestions = $questions->map(function ($question) use ($type) {
                return $this->normalizeQuestionFormat($question, $type);
            })->toArray();

            $questionsConfig[] = [
                'type' => $this->mapQuestionType($type),
                'count' => count($formattedQuestions),
                'difficulty' => $this->difficulty,
                'questions' => $formattedQuestions,
            ];
        }

        return $questionsConfig;
    }

    /**
     * Normalize question format to match system expectations
     */
    protected function normalizeQuestionFormat($question, $type): array
    {
        $normalized = [
            'id' => $question['id'] ?? uniqid('book_'),
            'question' => $question['question'] ?? '',
            'difficulty' => $question['difficulty'] ?? $this->difficulty,
            'points' => $question['points'] ?? 1,
            'explanation' => $question['explanation'] ?? null,
            'learning_objective' => $question['learning_objective'] ?? null,
            'type' => $this->mapQuestionType($type), // Add type to normalized data
        ];

        // Handle multiple choice questions - convert 0-indexed to A-E
        if ($type === 'multiple_choice') {
            $options = $question['options'] ?? [];
            $correctAnswerText = $question['correct_answer'] ?? null;

            // Convert numeric indices to letter indices
            $convertedOptions = [];
            $letters = ['A', 'B', 'C', 'D', 'E'];
            $correctAnswerLetter = null;

            foreach ($options as $index => $optionText) {
                // Determine the letter index
                if (is_string($index) && in_array($index, $letters)) {
                    $letterIndex = $index;
                } else {
                    $letterIndex = is_numeric($index) ? $letters[$index] : $letters[0];
                }

                $convertedOptions[$letterIndex] = $optionText;

                // Find which letter corresponds to the correct answer text
                if ($correctAnswerText && trim($optionText) === trim($correctAnswerText)) {
                    $correctAnswerLetter = $letterIndex;
                }
            }

            // If correct answer is already a letter, use it
            if ($correctAnswerText && in_array(strtoupper($correctAnswerText), $letters)) {
                $correctAnswerLetter = strtoupper($correctAnswerText);
            }

            // If we still don't have a correct answer letter, try to find it by matching text
            if (! $correctAnswerLetter && $correctAnswerText) {
                foreach ($convertedOptions as $letter => $optionText) {
                    if (strcasecmp(trim($optionText), trim($correctAnswerText)) === 0) {
                        $correctAnswerLetter = $letter;
                        break;
                    }
                }
            }

            $normalized['options'] = $convertedOptions;
            $normalized['correct_answer'] = $correctAnswerLetter;
            $normalized['answer'] = $correctAnswerLetter; // Add both for compatibility

            // Log warning if we couldn't find the correct answer
            if (! $correctAnswerLetter) {
                \Log::warning('Could not determine correct answer letter for question', [
                    'question_id' => $normalized['id'],
                    'correct_answer_text' => $correctAnswerText,
                    'options' => $convertedOptions,
                ]);
            }
        }
        // Handle true/false questions
        elseif ($type === 'true_false') {
            $normalized['correct_answer'] = $question['correct_answer'] ?? $question['answer'] ?? null;
            $normalized['answer'] = $normalized['correct_answer'];
        }
        // Handle essay questions
        elseif ($type === 'essay' || $type === 'essay_question') {
            $normalized['sample_answer'] = $question['sample_answer'] ?? null;
            $normalized['rubric'] = $question['rubric'] ?? null;
            $normalized['max_words'] = $question['max_words'] ?? null;
            $normalized['min_words'] = $question['min_words'] ?? null;
        }

        return $normalized;
    }

    protected function mapQuestionType($type): string
    {
        return match ($type) {
            'true_false', 'true_or_false_question' => 'true_or_false_question',
            'essay', 'essay_question' => 'essay_question',
            'multiple_choice', 'multiple_choice_question' => 'multiple_choice_question',
            default => 'multiple_choice_question'
        };
    }

    /**
     * Extract content from the selected book
     *
     * @return string Extracted content
     *
     * @throws MissingCatalogException
     */
    protected function extractBookContent(): string
    {
        if (! $this->selectedBook) {
            return '';
        }

        try {
            $relativePdfPath = $this->selectedBook->getAttributes()['content_url'] ?? null;
            if (! $relativePdfPath) {
                throw new \RuntimeException('Book PDF path not found');
            }

            $pdfPath = Storage::disk('public')->path($relativePdfPath);
            if (! file_exists($pdfPath)) {
                throw new \RuntimeException('Book PDF file not found');
            }

            // Extract content based on specified range
            if ($this->pageStart && $this->pageEnd) {
                // Extract specific page range
                return $this->pdfExtractor->extractPageRange(
                    $pdfPath,
                    (int) $this->pageStart,
                    (int) $this->pageEnd,
                    ['preserve_layout' => false]
                );
            } elseif ($this->selectedChapterId) {
                // Extract specific chapter
                $chapter = collect($this->selectedBook->formatted_table_of_contents)
                    ->firstWhere('chapter_number', $this->selectedChapterId);

                if ($chapter) {
                    return $this->pdfExtractor->extractPageRange(
                        $pdfPath,
                        $chapter['page_start'] ?? 1,
                        $chapter['page_end'] ?? $this->pdfExtractor->getPageCount($pdfPath),
                        ['preserve_layout' => false]
                    );
                }
            }

            // Extract entire book content
            return $this->pdfExtractor->extractText($pdfPath, [
                'preserve_layout' => false,
                'method' => 'auto',
            ]);

        } catch (\Exception $e) {
            \Log::error('Book content extraction failed', [
                'user_id' => Auth::id(),
                'book_id' => $this->selectedBookId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Start editing a question
     */
    public function startEditingQuestion(int $index): void
    {
        if (isset($this->generatedQuestions[$index])) {
            $this->editingQuestionIndex = $index;
            $this->editingQuestion = $this->generatedQuestions[$index];
        }
    }

    /**
     * Save the edited question
     */
    public function saveQuestion(): void
    {
        if ($this->editingQuestionIndex !== null && $this->editingQuestion) {
            // Validate the edited question
            if (empty($this->editingQuestion['question'])) {
                $this->addError('editingQuestion.question', 'Question text is required.');

                return;
            }

            // For multiple choice, validate options and correct answer
            if (($this->editingQuestion['type'] ?? '') === 'multiple_choice') {
                $options = array_filter($this->editingQuestion['options'] ?? []);
                if (count($options) < 2) {
                    $this->addError('editingQuestion.options', 'At least 2 options are required.');

                    return;
                }

                if (empty($this->editingQuestion['correct_answer'])) {
                    $this->addError('editingQuestion.correct_answer', 'Correct answer is required.');

                    return;
                }
            }

            // Update the question in the array
            $this->generatedQuestions[$this->editingQuestionIndex] = $this->editingQuestion;

            // Reset editing state
            $this->cancelEditing();

            $this->dispatch('question-updated');
        }
    }

    /**
     * Cancel editing
     */
    public function cancelEditing(): void
    {
        $this->editingQuestionIndex = null;
        $this->editingQuestion = null;
        $this->resetErrorBag('editingQuestion.*');
    }

    /**
     * Show delete confirmation for a question
     */
    public function confirmDeleteQuestion(int $index): void
    {
        $this->questionToDelete = $index;
        $this->showDeleteConfirmation = true;
    }

    /**
     * Cancel delete confirmation
     */
    public function cancelDelete(): void
    {
        $this->questionToDelete = null;
        $this->showDeleteConfirmation = false;
    }

    /**
     * Remove a question from the list
     */
    public function removeQuestion(): void
    {
        if ($this->questionToDelete !== null && isset($this->generatedQuestions[$this->questionToDelete])) {
            array_splice($this->generatedQuestions, $this->questionToDelete, 1);

            // Re-index the array
            $this->generatedQuestions = array_values($this->generatedQuestions);

            $this->cancelDelete();
            $this->dispatch('question-removed');
        }
    }

    /**
     * Move a question up in the list
     */
    public function moveQuestionUp(int $index): void
    {
        if ($index > 0 && isset($this->generatedQuestions[$index])) {
            $temp = $this->generatedQuestions[$index - 1];
            $this->generatedQuestions[$index - 1] = $this->generatedQuestions[$index];
            $this->generatedQuestions[$index] = $temp;

            $this->dispatch('question-reordered');
        }
    }

    /**
     * Move a question down in the list
     */
    public function moveQuestionDown(int $index): void
    {
        if ($index < count($this->generatedQuestions) - 1 && isset($this->generatedQuestions[$index])) {
            $temp = $this->generatedQuestions[$index + 1];
            $this->generatedQuestions[$index + 1] = $this->generatedQuestions[$index];
            $this->generatedQuestions[$index] = $temp;

            $this->dispatch('question-reordered');
        }
    }

    /**
     * Update marks for a specific question
     */
    public function updateQuestionMarks(int $index, $marks): void
    {
        if (isset($this->generatedQuestions[$index])) {
            $this->generatedQuestions[$index]['points'] = max(1, (int) $marks);
            $this->recalculateTotalMarks();
        }
    }

    /**
     * Recalculate total marks based on individual question marks
     */
    protected function recalculateTotalMarks(): void
    {
        $this->totalMarks = collect($this->generatedQuestions)->sum('points');
    }

    /**
     * Distribute marks evenly across all questions
     */
    public function distributeMarksEvenly(): void
    {
        if (empty($this->generatedQuestions)) {
            return;
        }

        $questionCount = count($this->generatedQuestions);
        $marksPerQuestion = max(1, (int) floor($this->totalMarks / $questionCount));
        $remainder = $this->totalMarks - ($marksPerQuestion * $questionCount);

        foreach ($this->generatedQuestions as $index => &$question) {
            // Distribute remainder to first questions
            $question['points'] = $marksPerQuestion + ($index < $remainder ? 1 : 0);
        }

        $this->dispatch('marks-distributed');
    }

    /**
     * Regenerate a single question
     */
    public function regenerateQuestion(int $index): void
    {
        if (! isset($this->generatedQuestions[$index])) {
            return;
        }

        $this->isRegenerating = true;
        $this->regeneratingIndex = $index;

        try {
            $currentQuestion = $this->generatedQuestions[$index];
            $content = '';

            // Get content based on source
            if ($this->selectedBookId) {
                $content = $this->extractBookContent();
            } else {
                $content = $this->fileContent;
            }

            if (empty($content)) {
                $this->addError('regeneration', 'Failed to extract content for regeneration.');
                $this->isRegenerating = false;
                $this->regeneratingIndex = null;

                return;
            }

            $parameters = [
                'book_id' => $this->selectedBookId,
                'question_type' => $currentQuestion['type'] ?? $this->questionType,
                'question_count' => 1,
                'difficulty' => $currentQuestion['difficulty'] ?? $this->difficulty,
                'focus_topics' => $this->parseFocusTopics(),
                'include_quotes' => $this->includeQuotes,
                'content' => $content,
                'file_content' => $this->fileContent ?: null,
                'file_name' => $this->fileName ?: null,
                'request_type' => 'single_question_regeneration',
                'exclude_questions' => array_column($this->generatedQuestions, 'question'),
            ];

            if ($this->selectedBook) {
                $parameters = array_merge($parameters, [
                    'book_title' => $this->selectedBook->title,
                    'author' => $this->selectedBook->author_name,
                    'genre' => $this->selectedBook->genre,
                ]);
            }

            $quizData = $this->bookLearningService->generateAdaptiveQuiz(
                Auth::user(),
                $this->selectedBook,
                $parameters
            );

            if (! empty($quizData['questions'][0])) {
                // Preserve the original points
                $originalPoints = $this->generatedQuestions[$index]['points'] ?? 1;
                $this->generatedQuestions[$index] = $quizData['questions'][0];
                $this->generatedQuestions[$index]['points'] = $originalPoints;

                $this->dispatch('question-regenerated');
            } else {
                $this->addError('regeneration', 'Failed to regenerate question. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Question regeneration failed', [
                'user_id' => Auth::id(),
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            $this->addError('regeneration', 'Unable to regenerate question. Please try again later.');
        } finally {
            $this->isRegenerating = false;
            $this->regeneratingIndex = null;
        }
    }

    /**
     * Get the computed total marks from questions
     */
    public function getComputedTotalMarksProperty(): int
    {
        return collect($this->generatedQuestions)->sum('points');
    }

    public function render()
    {
        return view('livewire.teachers.book-based-assignment');
    }
}
