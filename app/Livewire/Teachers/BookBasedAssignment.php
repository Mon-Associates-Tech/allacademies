<?php

namespace App\Livewire\Teachers;

use App\Models\Book;
use App\Models\AcademicSubject;
use App\Services\BookBasedLearningService;
use App\Services\AcademicChatService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class BookBasedAssignment extends Component
{
    use WithFileUploads;

    public $selectedBookId = '';
    public $selectedChapterId = '';
    public $pageStart = '';
    public $pageEnd = '';
    public $questionType = 'mixed';
    public $questionCount = 10;
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
    public $selectedSubjectId = ''; // New property for subject selection

    // Target groups
    public $selectedStudentGroups = [];
    public $selectedAcademicLevels = [];
    public $selectedAcademicGroups = [];
    public $selectedStudents = [];

    // Component state
    public $availableBooks = [];
    public $availableSubjects = []; // New property for subjects
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

    protected $rules = [
        'title' => 'required|string|max:255',
        'selectedSubjectId' => 'required|exists:academic_subjects,id', // Subject is now required
        'description' => 'nullable|string',
        'selectedBookId' => 'required_without:uploadedFile',
        'questionType' => 'required|in:multiple_choice,true_false,essay,mixed',
        'questionCount' => 'required|integer|min:1|max:50',
        'difficulty' => 'required|in:easy,medium,hard',
        'durationInMinutes' => 'required|integer|min:1',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after:startDate',
    ];

    protected BookBasedLearningService $bookLearningService;
    protected AcademicChatService $chatService;

    public function boot(
        BookBasedLearningService $bookLearningService,
        AcademicChatService $chatService
    ) {
        $this->bookLearningService = $bookLearningService;
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->loadAvailableBooks();
        $this->loadAvailableSubjects(); // Load subjects
        $this->loadTargetGroups();
        $this->startDate = now()->format('Y-m-d H:i');
        $this->endDate = now()->addWeek()->format('Y-m-d H:i');
    }

    protected function loadAvailableBooks()
    {
        $this->availableBooks = Book::published()
            ->with(['author', 'bookCategory'])
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
        if (!$this->selectedBook || !$this->selectedBook->table_of_contents) {
            $this->bookChapters = [];
            return;
        }

        $this->bookChapters = collect($this->selectedBook->formatted_table_of_contents)
            ->map(function ($chapter) {
                return (object)[
                    'id' => $chapter['chapter_number'],
                    'chapter_number' => $chapter['chapter_number'],
                    'title' => $chapter['title'],
                    'page_range' => $chapter['page_range']
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
            // Extract content from uploaded file
            $this->fileContent = $this->chatService->extractFileContent($this->uploadedFile);
            $this->fileName = $this->uploadedFile->getClientOriginalName();
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
        if (!$this->selectedBookId && empty($this->fileContent)) {
            $this->addError('selectedBookId', 'Please select a book or upload a file first.');
            return;
        }

        $this->isGenerating = true;

        try {
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
                'file_content' => $this->fileContent,
                'file_name' => $this->fileName,
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

            if (!empty($quizData['questions'])) {
                $this->generatedQuestions = $quizData['questions'];
                $this->dispatch('questions-generated');
            } else {
                $this->addError('generation', 'Failed to generate questions. Please try again with different parameters.');
            }

        } catch (\Exception $e) {
            \Log::error('Question generation failed', [
                'user_id' => Auth::id(),
                'book_id' => $this->selectedBookId,
                'error' => $e->getMessage()
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
        $this->validate();

        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            $this->addError('teacher', 'Teacher profile not found.');
            return;
        }

        try {
            // Create the assignment - following the existing structure
            $assignment = new \App\Models\Assignment();
            $assignment->teacher_id = $teacher->id;
            $assignment->academic_subject_id = $this->selectedSubjectId; // Now properly set
            $assignment->title = $this->title;
            $assignment->description = $this->description;
            $assignment->type = 'quiz';
            $assignment->duration_in_minutes = $this->durationInMinutes;
            $assignment->starts_at = $this->startDate;
            $assignment->ends_at = $this->endDate;
            $assignment->is_randomized = $this->isRandomized;
            $assignment->status = 'draft';
            $assignment->total_marks = $this->questionCount;
            $assignment->questions = $this->formatQuestionsForAssignment();
            $assignment->save();

            // Attach target groups
            if (!empty($this->selectedStudentGroups)) {
                $assignment->studentGroups()->attach($this->selectedStudentGroups);
            }

            if (!empty($this->selectedAcademicLevels)) {
                $assignment->academicLevels()->attach($this->selectedAcademicLevels);
            }

            if (!empty($this->selectedAcademicGroups)) {
                $assignment->academicGroups()->attach($this->selectedAcademicGroups);
            }

            if (!empty($this->selectedStudents)) {
                $assignment->students()->attach($this->selectedStudents);
            }

            $this->dispatch('assignment-created', ['id' => $assignment->id]);
            session()->flash('success', 'Assignment created successfully!');

        } catch (\Exception $e) {
            \Log::error('Assignment creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
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
            $questionsConfig[] = [
                'type' => $this->mapQuestionType($type),
                'count' => count($questions),
                'difficulty' => $this->difficulty,
                'questions' => $questions->toArray()
            ];
        }

        return $questionsConfig;
    }
    protected function mapQuestionType($type): string
    {
        return match($type) {
            'multiple_choice' => 'multiple_choice_question',
            'true_false' => 'true_or_false_question',
            'essay' => 'essay_question',
            default => 'multiple_choice_question'
        };
    }

    public function render()
    {
        return view('livewire.teachers.book-based-assignment');
    }
}
