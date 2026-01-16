<?php

namespace App\Livewire\Learning;

use App\Enums\SubscriptionStatus;
use App\Models\AcademicSubject;
use App\Models\Book;
use App\Models\BookReadingProgress;
use App\Models\QuizSession;
use App\Services\AcademicChatService;
use App\Services\BookBasedLearningService;
use App\Services\PdfContentExtractionService;
use App\Traits\ChecksTokenAvailability;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

class BookQuizInterface extends Component
{
    use ChecksTokenAvailability;
    use WithFileUploads;

    protected $listeners = [
        'update-selectedSubjectId' => 'updateSelectedSubject',
    ];

    // Quiz setup properties
    public $selectedBookId = '';

    public $selectedSubjectId = '';

    public $selectedChapterId = '';

    public $pageStart = '';

    public $pageEnd = '';

    #[Rule('required|in:multiple_choice,true_false,essay,mixed')]
    public $questionType = 'multiple_choice';

    #[Rule('required|integer|min:5|max:20')]
    public $questionCount = 10;

    public $customQuestionCount = 10;

    #[Rule('required|in:easy,medium,hard')]
    public $difficulty = 'medium';

    public $focusTopics = '';

    public $includeQuotes = false;

    // Component state
    public $availableBooks = [];

    public $bookChapters = [];

    public $selectedBook = null;

    public $quizData = null;

    public $quizResults = null;

    public $activeTab = 'new';

    public $isGenerating = false;

    public $previousQuizzes = [];

    public $showDetailedResults = false;

    public $showDetailedResultsModal = false;

    public $uploadedFile = null;

    public $fileContent = '';

    public $fileName = '';

    public $canGenerateQuiz = true;

    public $tokenWarningMessage = null;

    public $contentSourceTab = 'book';

    public $availableSubjects = [];

    protected $chatService;

    protected $bookLearningService;

    protected $pdfExtractor;

    public function boot(
        AcademicChatService $chatService,
        BookBasedLearningService $bookLearningService,
        PdfContentExtractionService $pdfExtractor
    ) {
        $this->chatService = $chatService;
        $this->bookLearningService = $bookLearningService;
        $this->pdfExtractor = $pdfExtractor;
    }

    public function mount(): void
    {
        $result = $this->checkTokenAvailability(500);
        $this->canGenerateQuiz = $result['available'];
        $this->tokenWarningMessage = $result['message'];

        $this->loadAvailableBooks();
        $this->loadAvailableSubjects();
        $this->loadPreviousQuizzes();

        // Check for quiz ID in URL
        $quizId = request()->query('quiz');
        if ($quizId) {
            $this->viewResults($quizId);
        }
    }

    protected function loadAvailableBooks(): void
    {
        $user = Auth::user();

        // Admins and owners can see all books
        if (in_array($user->role, ['admin', 'owner'])) {
            $this->availableBooks = Book::published()
                ->with(['author', 'bookCategory'])
                ->orderBy('title')
                ->get();

            return;
        }

        $student = $user->student ?? $user;

        // Start with published books
        $query = Book::published()
            ->with(['author', 'bookCategory']);

        // Filter to only include accessible books
        $query->where(function ($q) use ($user, $student) {
            // Free books
            // $q->where(function ($freeQuery) {
            //    $freeQuery->whereNull('annual_subscription_fee')
            //        ->orWhere('annual_subscription_fee', 0);
            // });

            // Books with individual subscriptions
            $q->orWhereHas('subscriptions', function ($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id)
                    ->where('status', SubscriptionStatus::PAID->value);
            });

            // Books with group subscriptions (if applicable)
            if ($student && method_exists($student, 'studentGroup') && $student->studentGroup) {
                $q->orWhereHas('groupSubscriptions', function ($groupQuery) use ($student) {
                    $groupQuery->where('student_group_id', $student->studentGroup->id)
                        ->where('status', SubscriptionStatus::PAID->value);
                });
            }
        });

        $this->availableBooks = $query->orderBy('title')->get();

    }

    protected function loadAvailableSubjects(): void
    {
        $this->availableSubjects = AcademicSubject::with(['academicLevel.academicGroup'])
            ->orderBy('name')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'display_name' => $subject->name.' - '.
                        ($subject->academicLevel->academicGroup->name ?? 'N/A').' ('.
                        ($subject->academicLevel->name ?? 'N/A').')',
                    'academic_level' => $subject->academicLevel->name ?? 'N/A',
                    'academic_group' => $subject->academicLevel->academicGroup->name ?? 'N/A',
                ];
            })
            ->toArray();
    }

    public function updateSelectedSubject($value)
    {
        $this->selectedSubjectId = is_array($value) ? ($value[0] ?? '') : $value;
    }

    protected function loadPreviousQuizzes(): void
    {
        $this->previousQuizzes = QuizSession::where('user_id', Auth::id())
            ->with(['book.author', 'book.bookCategory', 'subject']) // Load subject relationship
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function updatedSelectedBookId(): void
    {
        if ($this->selectedBookId) {
            $this->selectedBook = Book::with(['author', 'bookCategory', 'subject'])->find($this->selectedBookId);
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

    protected function loadBookChapters(): void
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

    protected function resetPageRange(): void
    {
        $this->pageStart = '';
        $this->pageEnd = '';
    }

    public function updatedUploadedFile(): void
    {
        $this->validateOnly('uploadedFile');

        if ($this->uploadedFile) {
            try {
                $extension = strtolower($this->uploadedFile->getClientOriginalExtension());

                Log::info('File upload initiated', [
                    'user_id' => Auth::id(),
                    'file_name' => $this->uploadedFile->getClientOriginalName(),
                    'extension' => $extension,
                    'size' => $this->uploadedFile->getSize(),
                    'mime_type' => $this->uploadedFile->getMimeType(),
                ]);

                // Support multiple file types
                if (! in_array($extension, ['pdf', 'doc', 'docx', 'txt'])) {
                    $this->addError('uploadedFile', 'Unsupported file type. Please upload PDF, DOC, DOCX, or TXT files.');

                    return;
                }

                // Check file size (max 10MB)
                if ($this->uploadedFile->getSize() > 10 * 1024 * 1024) {
                    $this->addError('uploadedFile', 'File is too large. Maximum file size is 10MB.');

                    return;
                }

                // Check if file is empty
                if ($this->uploadedFile->getSize() === 0) {
                    $this->addError('uploadedFile', 'The uploaded file is empty.');

                    return;
                }

                // Extract content from uploaded file using the PDF extraction service
                try {
                    // For PDFs, limit to first 10 pages
                    if ($extension === 'pdf') {
                        $tempPath = $this->uploadedFile->getRealPath();
                        $pageCount = $this->pdfExtractor->getPageCount($tempPath);

                        if ($pageCount > 10) {
                            $this->fileContent = $this->pdfExtractor->extractPageRange(
                                $tempPath,
                                1,
                                10,
                                ['preserve_layout' => false]
                            );

                            Log::info('PDF limited to 10 pages', [
                                'user_id' => Auth::id(),
                                'file_name' => $this->uploadedFile->getClientOriginalName(),
                                'total_pages' => $pageCount,
                            ]);
                        } else {
                            $this->fileContent = $this->pdfExtractor->extractFromUploadedFile($this->uploadedFile, [
                                'preserve_layout' => false,
                                'method' => 'auto',
                            ]);
                        }
                    } else {
                        $this->fileContent = $this->pdfExtractor->extractFromUploadedFile($this->uploadedFile, [
                            'preserve_layout' => false,
                            'method' => 'auto',
                        ]);
                    }
                } catch (InvalidArgumentException $e) {
                    // Unsupported file type
                    $this->addError('uploadedFile', $e->getMessage());

                    return;
                } catch (RuntimeException $e) {
                    // Extraction failed
                    $this->addError('uploadedFile', 'Unable to extract content: '.$e->getMessage());
                    Log::error('Content extraction runtime error', [
                        'user_id' => Auth::id(),
                        'file_name' => $this->uploadedFile->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);

                    return;
                }

                $this->fileName = $this->uploadedFile->getClientOriginalName();

                if (empty($this->fileContent)) {
                    $this->addError('uploadedFile', 'No content could be extracted from the uploaded file. The file may be empty or corrupted.');
                    Log::warning('Empty content extracted', [
                        'user_id' => Auth::id(),
                        'file_name' => $this->fileName,
                    ]);

                    return;
                }

                Log::info('File content extracted successfully', [
                    'user_id' => Auth::id(),
                    'file_name' => $this->fileName,
                    'content_length' => strlen($this->fileContent),
                    'preview' => substr($this->fileContent, 0, 200),
                ]);

            } catch (Exception $e) {
                Log::error('File content extraction failed - unexpected error', [
                    'user_id' => Auth::id(),
                    'file_name' => $this->uploadedFile->getClientOriginalName() ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $this->addError('uploadedFile', 'An unexpected error occurred while processing the file. Please try again or use a different file.');
            }
        }
    }

    public function showResults(): void
    {
        if (! $this->quizResults) {
            return;
        }

        $this->showDetailedResults = true;
        $this->showDetailedResultsModal = true;
    }

    public function closeDetailedResults(): void
    {
        $this->showDetailedResults = false;
        $this->showDetailedResultsModal = false;
    }

    public function continueQuiz($quizSessionId): void
    {
        $quizSession = QuizSession::where('user_id', Auth::id())
            ->where('id', $quizSessionId)
            ->first();

        if ($quizSession) {
            // Only set book ID if the quiz was based on a book
            if ($quizSession->book_id) {
                $this->selectedBookId = $quizSession->book_id;
                $this->updatedSelectedBookId();
            }

            // Update URL with quiz ID
            $this->dispatch('update-url', ['quiz' => $quizSessionId]);
        }
    }

    public function viewResults($quizSessionId): void
    {
        $quizSession = QuizSession::where('user_id', Auth::id())
            ->where('id', $quizSessionId)
            ->with('book')
            ->first();

        if ($quizSession && $quizSession->results) {
            // Load the book if it exists
            $book = $quizSession->book;

            $this->quizResults = [
                'results' => $quizSession->results,
                'detailed_feedback' => $this->generateDetailedFeedback($quizSession->results, $quizSession),
                'question_breakdown' => $quizSession->results['question_details'] ?? [],
                'improvement_suggestions' => $this->getImprovementSuggestions($quizSession->results),
                'badges_earned' => [],
            ];

            // Try to get next steps if method exists and book is available
            if ($book && method_exists($this->bookLearningService, 'getNextLearningSteps')) {
                try {
                    $this->quizResults['next_steps'] = $this->bookLearningService->getNextLearningSteps(
                        Auth::user(),
                        $book,
                        $quizSession->results
                    );
                } catch (Exception $e) {
                    Log::warning('Next learning steps failed', [
                        'user_id' => Auth::id(),
                        'error' => $e->getMessage(),
                    ]);
                    $this->quizResults['next_steps'] = [];
                }
            } else {
                $this->quizResults['next_steps'] = [];
            }
            $this->activeTab = 'results';

            // Update URL with quiz ID
            $this->dispatch('update-url', ['quiz' => $quizSessionId]);
        }
    }

    protected function generateDetailedFeedback(array $results, QuizSession $session): array
    {
        $percentage = $results['percentage'];
        $book = $session->book;

        $feedback = [
            'overall_performance' => $this->getOverallPerformanceFeedback($percentage),
            'strengths' => [],
            'areas_for_improvement' => [],
            'study_suggestions' => [],
        ];

        // Analyze performance by question type
        $typePerformance = [];
        foreach ($results['question_details'] as $detail) {
            $type = $detail['question_type'];
            if (! isset($typePerformance[$type])) {
                $typePerformance[$type] = ['correct' => 0, 'total' => 0];
            }
            $typePerformance[$type]['total']++;
            if ($detail['is_correct']) {
                $typePerformance[$type]['correct']++;
            }
        }

        // Generate strengths and improvement areas
        foreach ($typePerformance as $type => $stats) {
            $accuracy = $stats['total'] > 0 ? ($stats['correct'] / $stats['total']) * 100 : 0;

            if ($accuracy >= 80) {
                $feedback['strengths'][] = $this->getStrengthMessage($type, $accuracy);
            } elseif ($accuracy < 60) {
                $feedback['areas_for_improvement'][] = $this->getImprovementMessage($type, $accuracy);
            }
        }

        // Add study suggestions
        $feedback['study_suggestions'] = $this->getStudySuggestions($results, $book);

        return $feedback;
    }

    protected function getOverallPerformanceFeedback(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'Excellent work! You have a strong understanding of the material and show great reading comprehension skills.';
        } elseif ($percentage >= 80) {
            return 'Great job! You demonstrate good comprehension with room for some improvement in specific areas.';
        } elseif ($percentage >= 70) {
            return 'Good effort! You understand the basics but could benefit from deeper analysis of the text.';
        } elseif ($percentage >= 60) {
            return "You're making progress. Consider reviewing the material and focusing on the main themes and characters.";
        } else {
            return "This material seems challenging. Let's work on building your reading comprehension step by step.";
        }
    }

    protected function getStrengthMessage(string $type, float $accuracy): string
    {
        $typeNames = [
            'multiple_choice' => 'factual comprehension',
            'true_false' => 'detail recognition',
            'essay' => 'analytical thinking',
        ];

        $typeName = $typeNames[$type] ?? $type;

        return "You excel at {$typeName} (".round($accuracy).'% accuracy).';
    }

    protected function getImprovementMessage(string $type, float $accuracy): string
    {
        $suggestions = [
            'multiple_choice' => 'Focus on carefully reading each question and all answer choices before selecting.',
            'true_false' => 'Pay attention to specific details and avoid absolute statements.',
            'essay' => 'Work on developing your ideas with specific examples from the text.',
        ];

        $suggestion = $suggestions[$type] ?? "Practice more {$type} questions.";

        return $suggestion.' (Current accuracy: '.round($accuracy).'%)';
    }

    protected function getStudySuggestions(array $results, $book): array
    {
        $suggestions = [];
        $percentage = $results['percentage'];

        // Handle both book objects and file uploads
        $author = $book ? ($book->author_name ?? 'the author') : 'the author';

        if ($percentage < 70) {
            $suggestions[] = 'Re-read key chapters focusing on main themes and character development';
            $suggestions[] = 'Create a character map to track relationships and motivations';
            $suggestions[] = 'Keep a reading journal with chapter summaries';
        } elseif ($percentage < 85) {
            $suggestions[] = 'Practice identifying literary devices and their purposes';
            $suggestions[] = 'Discuss the book with classmates or join online book discussions';
            $suggestions[] = "Research the historical context of the book's setting";
        } else {
            $suggestions[] = 'Explore critical essays about this work for deeper insights';
            if ($author !== 'the author') {
                $suggestions[] = "Read other books by {$author} for comparison";
            }
            $suggestions[] = "Consider the book's influence on later literature";
        }

        // Add book-specific suggestions only if we have a book object
        if ($book) {
            if ($book->has_audio) {
                $suggestions[] = 'Listen to the audio version to improve comprehension';
            }

            if ($book->bookCategory && str_contains(strtolower($book->bookCategory->name), 'classic')) {
                $suggestions[] = 'Research the time period when this classic was written';
            }
        } else {
            // General suggestions for file uploads
            $suggestions[] = 'Take notes while reading to track important information';
            $suggestions[] = 'Look up unfamiliar words or concepts';
        }

        return $suggestions;
    }

    protected function getImprovementSuggestions(array $results): array
    {
        $suggestions = [];
        $percentage = $results['percentage'];

        // Question-specific analysis
        $missedQuestions = collect($results['question_details'])
            ->where('is_correct', false);

        $missedTypes = $missedQuestions->groupBy('question_type');

        foreach ($missedTypes as $type => $questions) {
            $count = $questions->count();
            switch ($type) {
                case 'multiple_choice':
                    $suggestions[] = "Review multiple choice strategies: eliminate obviously wrong answers first ({$count} missed)";
                    break;
                case 'true_false':
                    $suggestions[] = "Be careful with true/false questions - watch for absolute words like 'always' or 'never' ({$count} missed)";
                    break;
                case 'essay':
                    $suggestions[] = "For essay questions, structure your answers with clear introduction, body, and conclusion ({$count} missed)";
                    break;
            }
        }

        // General suggestions based on performance
        if ($percentage < 60) {
            $suggestions[] = 'Consider slowing down your reading pace to improve comprehension';
            $suggestions[] = 'Take notes while reading to track important information';
        }

        if (empty($suggestions)) {
            $suggestions[] = 'Great work! Continue practicing with different types of questions to maintain your skills';
        }

        return $suggestions;
    }

    public function backToHistory(): void
    {
        $this->activeTab = 'history';
        $this->quizResults = null;

        // Clear URL parameter
        $this->dispatch('update-url', ['quiz' => null]);
    }

    public function generateQuiz(): void
    {
        $result = $this->checkTokenAvailability(500);
        $this->canGenerateQuiz = $result['available'];
        $this->tokenWarningMessage = $result['message'];

        if (! $this->canGenerateQuiz) {
            $this->dispatch('tokenCheckFailed');

            return;
        }

        $this->reset(['quizResults', 'activeTab']);

        $this->validate([
            'selectedSubjectId' => 'required|exists:academic_subjects,id',
            'questionType' => 'required|in:multiple_choice,true_false,essay,mixed',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        // Check if either a book is selected or a file is uploaded
        if (! $this->selectedBookId && empty($this->fileContent)) {
            $this->addError('selectedBookId', 'Please select a book or upload a file first.');

            return;
        }

        // Validate book exists if a book is selected
        if ($this->selectedBookId) {
            $this->validate([
                'selectedBookId' => 'required|exists:books,id',
            ]);

            // Load the book
            $this->selectedBook = Book::with(['author', 'bookCategory', 'subject'])->find($this->selectedBookId);
        } else {
            // Clear book reference for file uploads
            $this->selectedBook = null;
        }

        $actualQuestionCount = $this->getActualQuestionCount();

        // Validate question count
        if ($actualQuestionCount < 1 || $actualQuestionCount > 50) {
            $this->addError('questionCount', 'Number of questions must be between 1 and 50.');
            $this->isGenerating = false;

            return;
        }

        $this->isGenerating = true;
        $this->errors = [];

        try {
            $content = '';

            // FIX: Only extract book content if a book is actually selected
            if ($this->selectedBookId && $this->selectedBook) {
                try {
                    $content = $this->extractBookContent();
                } catch (Exception $e) {
                    Log::error('Book content extraction failed', [
                        'user_id' => Auth::id(),
                        'book_id' => $this->selectedBookId,
                        'error' => $e->getMessage(),
                    ]);

                    // If extraction fails but we have uploaded content, use it as fallback
                    if (! empty($this->fileContent)) {
                        Log::info('Using uploaded file as fallback after book extraction failure');
                        $content = $this->fileContent;
                    } else {
                        $this->addError('generation', $e->getMessage());
                        $this->isGenerating = false;

                        return;
                    }
                }
            } else {
                // Use uploaded file content
                $content = $this->fileContent;

                Log::info('Using uploaded file content for quiz generation', [
                    'user_id' => Auth::id(),
                    'file_name' => $this->fileName,
                    'content_length' => strlen($content),
                ]);
            }

            if (empty($content)) {
                $this->addError('generation', 'Failed to extract content. Please try again.');
                $this->isGenerating = false;

                return;
            }

            // FIX: Only set book_id if a book is actually selected
            $parameters = [
                'book_id' => $this->selectedBookId ?: null, // NULL for uploaded files
                'subject_id' => $this->selectedSubjectId,
                'chapter_id' => $this->selectedChapterId ?: null,
                'page_start' => $this->pageStart ?: null,
                'page_end' => $this->pageEnd ?: null,
                'question_type' => $this->questionType,
                'question_count' => $actualQuestionCount,
                'difficulty' => $this->difficulty,
                'focus_topics' => $this->parseFocusTopics(),
                'include_quotes' => $this->includeQuotes,
                'content' => $content,
                'file_content' => $this->fileContent ?: null,
                'file_name' => $this->fileName ?: null,
                'request_type' => 'quiz_generation',
                'is_file_based' => empty($this->selectedBookId), // NEW: Flag for file-based quizzes
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
            } else {
                // For uploaded files, provide basic metadata
                $parameters = array_merge($parameters, [
                    'book_title' => $this->fileName ?: 'Uploaded Content',
                    'author' => 'User Uploaded',
                    'genre' => 'General',
                    'themes' => [],
                    'difficulty_score' => 5,
                ]);
            }

            // FIX: Pass NULL for book when using uploaded files
            $bookForGeneration = $this->selectedBook ?: null;

            Log::info('Generating quiz with parameters', [
                'user_id' => Auth::id(),
                'has_book' => ! is_null($bookForGeneration),
                'book_id' => $parameters['book_id'],
                'is_file_based' => $parameters['is_file_based'],
                'content_length' => strlen($content),
            ]);

            // Generate adaptive quiz using the book learning service
            $quizData = $this->bookLearningService->generateAdaptiveQuiz(
                Auth::user(),
                $bookForGeneration, // Pass NULL when using uploaded files
                $parameters
            );

            // Handle errors...
            if (! $quizData || (isset($quizData['success']) && $quizData['success'] === false)) {
                $errorMessage = $quizData['error'] ?? 'Failed to generate quiz questions.';
                $errorCode = $quizData['error_code'] ?? null;

                if ($errorCode === 'BOOK_NOT_FOUND') {
                    $this->addError('selectedBookId', 'The selected book could not be found. Please try a different book or upload your own content.');
                    $this->selectedBookId = null;
                    $this->selectedBook = null;
                } elseif ($errorCode === 'INSUFFICIENT_CONTENT') {
                    $this->addError('generation', $errorMessage);

                    if (! empty($quizData['suggestions'])) {
                        foreach ($quizData['suggestions'] as $suggestion) {
                            $this->addError('suggestions', $suggestion);
                        }
                    }
                } else {
                    $this->addError('generation', $errorMessage);
                }

                Log::warning('Quiz generation failed gracefully', [
                    'user_id' => Auth::id(),
                    'book_id' => $this->selectedBookId,
                    'subject_id' => $this->selectedSubjectId,
                    'error_code' => $errorCode,
                    'has_file_fallback' => ! empty($this->fileContent),
                ]);

                $this->isGenerating = false;

                return;
            }

            if (! empty($quizData['questions'])) {
                // Create quiz session
                $this->createQuizSession($quizData, $parameters);
                $this->quizData = $quizData;
                Log::debug('Quiz data set', [
                    'has_quiz_data' => ! empty($this->quizData),
                    'question_count' => count($this->quizData['questions'] ?? []),
                    'quiz_data_keys' => array_keys($this->quizData ?? []),
                ]);
                $this->dispatch('quiz-generated', [
                    'questionCount' => count($quizData['questions']),
                ]);
                $this->dispatch('$refresh');
            } else {
                $this->addError('generation', 'Failed to generate quiz questions. Please try again with different parameters.');
            }

        } catch (Exception $e) {
            Log::error('Quiz generation failed with exception', [
                'user_id' => Auth::id(),
                'book_id' => $this->selectedBookId,
                'subject_id' => $this->selectedSubjectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->addError('generation', 'Unable to generate quiz. Please try different parameters or try again later.');
        } finally {
            $this->isGenerating = false;
        }
    }

    public function getActualQuestionCount(): int
    {
        if ($this->questionCount === 'custom') {
            // Validate custom value is within range
            return (int) $this->customQuestionCount;

            return max(1, min(50, (int) $this->customQuestionCount));
        }

        return (int) $this->questionCount;
    }

    /**
     * Extract content from the selected book
     *
     * @return string Extracted content
     *
     * @throws Exception
     */
    protected function extractBookContent(): string
    {
        if (! $this->selectedBook) {
            return '';
        }

        try {
            $relativePdfPath = $this->selectedBook->getAttributes()['content_url'] ?? null;
            if (! $relativePdfPath) {
                throw new RuntimeException('Book PDF path not found');
            }

            $pdfPath = Storage::disk('public')->path($relativePdfPath);
            if (! file_exists($pdfPath)) {
                throw new RuntimeException('Book PDF file not found');
            }

            // Extract content based on specified range
            if ($this->pageStart && $this->pageEnd) {
                $pageCount = (int) $this->pageEnd - (int) $this->pageStart + 1;
                if ($pageCount > 10) {
                    throw new RuntimeException('Page range exceeds 10 pages. Please select a maximum of 10 pages.');
                }

                return $this->pdfExtractor->extractPageRange(
                    $pdfPath,
                    (int) $this->pageStart,
                    (int) $this->pageEnd,
                    ['preserve_layout' => false]
                );
            } elseif ($this->selectedChapterId) {
                $chapter = $this->bookChapters->firstWhere('id', $this->selectedChapterId);

                if ($chapter) {
                    $chapterPageCount = ($chapter->page_end ?? 1) - ($chapter->page_start ?? 1) + 1;
                    if ($chapterPageCount > 10) {
                        throw new RuntimeException('Selected chapter exceeds 10 pages. Please specify a page range within the chapter (max 10 pages).');
                    }

                    return $this->pdfExtractor->extractPageRange(
                        $pdfPath,
                        $chapter->page_start ?? 1,
                        $chapter->page_end ?? $this->pdfExtractor->getPageCount($pdfPath),
                        ['preserve_layout' => false]
                    );
                }
            }

            // Default: Extract first 10 pages only
            return $this->pdfExtractor->extractPageRange(
                $pdfPath,
                1,
                10,
                ['preserve_layout' => false]
            );

        } catch (Exception $e) {
            Log::error('Book content extraction failed', [
                'user_id' => Auth::id(),
                'book_id' => $this->selectedBookId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function parseFocusTopics(): array
    {
        if (empty($this->focusTopics)) {
            return [];
        }

        return array_map('trim', explode(',', $this->focusTopics));
    }

    protected function createQuizSession(array $quizData, array $parameters): void
    {
        $questions = $quizData['questions'] ?? [];
        $questions = $this->randomizeQuestionOptions($questions);

        $cleanedQuestions = [];
        foreach ($questions as $question) {
            if (is_array($question)) {
                $cleanedQuestions[] = $question;
            }
        }

        $sessionStartTime = now();

        $quizSessionData = [
            'user_id' => Auth::id(),
            'book_id' => $this->selectedBookId ?: null, // NULL for uploaded content
            'subject_id' => $this->selectedSubjectId, // ALWAYS required
            'chapter_id' => $parameters['chapter_id'] ?? null,
            'page_start' => $parameters['page_start'] ?? null,
            'page_end' => $parameters['page_end'] ?? null,
            'question_type' => $parameters['question_type'],
            'question_count' => $parameters['question_count'],
            'difficulty' => $parameters['difficulty'],
            'questions' => $cleanedQuestions,
            'context' => $this->buildQuizContext($parameters),
            'status' => 'active',
            'started_at' => $sessionStartTime,
        ];

        $session = QuizSession::create($quizSessionData);

        if ($this->quizData) {
            $this->quizData['session_id'] = $session->id;
            $this->quizData['session_started_at'] = $sessionStartTime;
        }
    }

    protected function randomizeQuestionOptions(array $questions): array
    {
        foreach ($questions as &$question) {
            if (isset($question['type']) && $question['type'] === 'multiple_choice' && isset($question['options'])) {
                // Store the correct answer
                $correctAnswer = $question['correct_answer'];

                // Shuffle the options
                shuffle($question['options']);

                // Update the correct answer index if needed
                $question['correct_answer'] = $correctAnswer;
            }
        }

        return $questions;
    }

    protected function buildQuizContext(array $parameters): array
    {
        $context = [];

        // Only include book-related context if a book is selected
        if ($this->selectedBook) {
            $context = [
                'book_title' => $this->selectedBook->title,
                'author' => $this->selectedBook->author_name,
                'book_category' => $this->selectedBook->bookCategory->name ?? 'General',
                'genre' => $this->selectedBook->genre,
                'difficulty_score' => $this->selectedBook->difficulty_score,
                'themes' => $this->selectedBook->themes ?? [],
            ];
        } else {
            // For file uploads, use file name as the title
            $context = [
                'book_title' => $this->fileName ?? 'Uploaded Content',
                'author' => 'User Uploaded',
                'book_category' => 'Uploaded Content',
                'genre' => 'General',
                'difficulty_score' => 5,
                'themes' => [],
            ];
        }

        if ($parameters['chapter_id']) {
            $chapter = $this->bookChapters->firstWhere('id', $parameters['chapter_id']);
            $context['chapter'] = $chapter ? $chapter->title : null;
        }

        if ($parameters['page_start'] && $parameters['page_end']) {
            $context['page_range'] = "Pages {$parameters['page_start']}-{$parameters['page_end']}";
        }

        if (! empty($parameters['focus_topics'])) {
            $context['focus_topics'] = $parameters['focus_topics'];
        }

        return $context;
    }

    public function submitQuizAnswers($answers, $timeTaken = null): void
    {
        \Log::info('Quiz submission started', [
            'user_id' => Auth::id(),
            'answers_count' => count($answers ?? []),
            'answers' => $answers,
            'time_taken' => $timeTaken,
        ]);

        if (! $this->quizData || empty($answers)) {
            \Log::warning('Quiz submission failed - no quiz data or answers', [
                'quiz_data_exists' => ! empty($this->quizData),
                'answers_exists' => ! empty($answers),
            ]);
            $this->addError('submission', 'No quiz data or answers found.');

            return;
        }

        try {
            $quizSession = QuizSession::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('started_at', $this->quizData['session_started_at'] ?? now())
                ->first();

            // Fallback to the original method if the specific query fails:
            if (! $quizSession) {
                $quizSession = QuizSession::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->latest()
                    ->first();
            }

            if (! $quizSession) {
                \Log::warning('Quiz session not found', ['user_id' => Auth::id()]);
                $this->addError('submission', 'Quiz session not found.');

                return;
            }

            \Log::info('Grading quiz', ['quiz_session_id' => $quizSession->id]);

            // Grade the quiz
            $gradingResult = $this->gradeQuiz($quizSession, $answers);

            \Log::info('Quiz graded successfully', [
                'total_questions' => $gradingResult['total_questions'] ?? 0,
                'correct_answers' => $gradingResult['correct_answers'] ?? 0,
                'percentage' => $gradingResult['percentage'] ?? 0,
            ]);

            // Update quiz session
            $quizSession->update([
                'answers' => $answers,
                'results' => $gradingResult,
                'time_taken' => $timeTaken,
                'completed_at' => now(),
                'status' => 'completed',
            ]);

            \Log::info('Quiz session updated', ['quiz_session_id' => $quizSession->id]);

            // Update reading progress
            $this->updateReadingProgress($gradingResult);

            // Check for achievements (with error handling)
            $achievements = [];
            try {
                if (method_exists($this->bookLearningService, 'checkAndAwardAchievements')) {
                    $achievements = $this->bookLearningService->checkAndAwardAchievements(
                        Auth::user(),
                        'quiz_completed',
                        ['results' => $gradingResult]
                    );
                }
            } catch (Exception $e) {
                \Log::warning('Achievement checking failed', [
                    'user_id' => Auth::id(),
                    'error' => $e->getMessage(),
                ]);
            }

            // Generate feedback
            $feedback = $this->generateDetailedFeedback($gradingResult, $quizSession);

            // Prepare results for display
            $this->quizResults = [
                'results' => $gradingResult,
                'detailed_feedback' => $feedback,
                'question_breakdown' => $gradingResult['question_details'] ?? [],
                'improvement_suggestions' => $this->getImprovementSuggestions($gradingResult),
                'badges_earned' => $achievements,
            ];

            // Only try to get next steps if method exists
            if (method_exists($this->bookLearningService, 'getNextLearningSteps')) {
                try {
                    $this->quizResults['next_steps'] = $this->bookLearningService->getNextLearningSteps(
                        Auth::user(),
                        $this->selectedBook,
                        $gradingResult
                    );
                } catch (Exception $e) {
                    \Log::warning('Next learning steps failed', [
                        'user_id' => Auth::id(),
                        'error' => $e->getMessage(),
                    ]);
                    $this->quizResults['next_steps'] = [];
                }
            } else {
                $this->quizResults['next_steps'] = [];
            }
            $this->loadPreviousQuizzes();
            \Log::info('Quiz results prepared successfully');

        } catch (Exception $e) {
            \Log::error('Quiz submission failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->addError('submission', 'Failed to submit quiz. Please try again. Error: '.$e->getMessage());
        }
    }

    protected function gradeQuiz(QuizSession $quizSession, array $answers): array
    {
        $questions = $quizSession->questions;
        $totalQuestions = count($questions);
        $correctAnswers = 0;
        $questionDetails = [];

        foreach ($questions as $index => $question) {
            $userAnswer = $answers[$index] ?? null;
            $isCorrect = false;
            $feedback = '';
            $pointsEarned = 0;

            // Safely get question type with fallback
            $questionType = $question['type'] ?? $question['question_type'] ?? 'unknown';

            switch ($questionType) {
                case 'multiple_choice':
                case 'true_false':
                    // Safely get correct answer
                    $correctAnswer = $question['correct_answer'] ?? '';
                    $isCorrect = strtolower((string) $userAnswer) === strtolower((string) $correctAnswer);
                    $feedback = $question['explanation'] ?? '';
                    $pointsEarned = $isCorrect ? ($question['points'] ?? 1) : 0;
                    break;

                case 'essay':
                case 'essay_question':
                    // FIX: Properly grade essay questions
                    $gradingResult = $this->gradeEssayQuestion($question, $userAnswer, $quizSession);

                    // Use the score from grading (0-100) to determine correctness
                    $score = $gradingResult['score'] ?? 0;

                    // Consider passing if score is 60% or higher
                    $isCorrect = $score >= 60;

                    // Calculate points proportionally
                    $maxPoints = $question['points'] ?? 1;
                    $pointsEarned = ($score / 100) * $maxPoints;

                    $feedback = $gradingResult['feedback'] ?? 'Your essay has been reviewed.';

                    Log::info('Essay question graded', [
                        'question_index' => $index,
                        'score' => $score,
                        'points_earned' => $pointsEarned,
                        'max_points' => $maxPoints,
                        'is_correct' => $isCorrect,
                    ]);
                    break;

                default:
                    // Handle unknown question types
                    $isCorrect = false;
                    $feedback = 'Unable to grade this question type.';
                    $pointsEarned = 0;
                    break;
            }

            if ($isCorrect && $questionType !== 'essay' && $questionType !== 'essay_question') {
                $correctAnswers++;
            }

            $questionDetails[] = [
                'question_number' => $index + 1,
                'question_text' => $question['question'] ?? 'Question text not available',
                'user_answer' => $userAnswer,
                'correct_answer' => $question['correct_answer'] ?? 'N/A',
                'is_correct' => $isCorrect,
                'points_earned' => round($pointsEarned, 2),
                'points_possible' => $question['points'] ?? 1,
                'feedback' => $feedback,
                'question_type' => $questionType,
                'essay_score' => $questionType === 'essay' || $questionType === 'essay_question' ? ($score ?? 0) : null,
            ];
        }

        // Calculate total points
        $totalPointsEarned = array_sum(array_column($questionDetails, 'points_earned'));
        $totalPointsPossible = array_sum(array_column($questionDetails, 'points_possible'));

        $percentage = $totalPointsPossible > 0 ? ($totalPointsEarned / $totalPointsPossible) * 100 : 0;

        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'percentage' => round($percentage, 2),
            'question_details' => $questionDetails,
            'points_earned' => round($totalPointsEarned, 2),
            'points_possible' => $totalPointsPossible,
        ];
    }

    protected function gradeEssayQuestion(array $question, ?string $answer, QuizSession $quizSession): array
    {
        if (empty($answer) || strlen(trim($answer)) < 10) {
            return [
                'score' => 0,
                'feedback' => empty($answer)
                    ? 'No answer provided.'
                    : 'Your answer is too short. Please provide a more detailed response (minimum 10 characters).',
            ];
        }

        // Safely get book title with fallbacks
        $bookTitle = 'Unknown Book';
        if ($quizSession->book) {
            $bookTitle = $quizSession->book->title ?? 'Unknown Book';
        } elseif (isset($quizSession->context['book_title'])) {
            $bookTitle = $quizSession->context['book_title'];
        }

        // Use AI to grade essay with enhanced prompt
        $gradingPrompt = "Grade this essay answer for the book '{$bookTitle}':\n\n";
        $gradingPrompt .= 'Question: '.($question['question'] ?? 'No question text provided')."\n\n";
        $gradingPrompt .= "Student Answer: {$answer}\n\n";
        $gradingPrompt .= 'Expected Answer: '.($question['correct_answer'] ?? 'No expected answer provided')."\n\n";
        $gradingPrompt .= "GRADING CRITERIA:\n";
        $gradingPrompt .= "1. Content Understanding (40%): Does the answer demonstrate understanding of the text?\n";
        $gradingPrompt .= "2. Analysis Depth (30%): Does the answer provide thoughtful analysis?\n";
        $gradingPrompt .= "3. Textual Evidence (20%): Are specific examples from the text included?\n";
        $gradingPrompt .= "4. Writing Clarity (10%): Is the answer well-organized and clearly written?\n\n";
        $gradingPrompt .= "Compare the student's answer with the expected answer and provide a similarity score.\n";
        $gradingPrompt .= "PROVIDE:\n";
        $gradingPrompt .= "- A score from 0-100 based on similarity and quality\n";
        $gradingPrompt .= "- Specific feedback on each grading criterion\n";
        $gradingPrompt .= "- Suggestions for improvement\n";
        $gradingPrompt .= "- Overall assessment\n\n";
        $gradingPrompt .= "FORMAT RESPONSE AS JSON:\n";
        $gradingPrompt .= "{\n";
        $gradingPrompt .= "  \"score\": 0-100,\n";
        $gradingPrompt .= "  \"feedback\": \"Detailed feedback\",\n";
        $gradingPrompt .= "  \"criteria\": {\n";
        $gradingPrompt .= "    \"content_understanding\": \"Feedback\",\n";
        $gradingPrompt .= "    \"analysis_depth\": \"Feedback\",\n";
        $gradingPrompt .= "    \"textual_evidence\": \"Feedback\",\n";
        $gradingPrompt .= "    \"writing_clarity\": \"Feedback\"\n";
        $gradingPrompt .= "  },\n";
        $gradingPrompt .= "  \"suggestions\": [\"Improvement suggestions\"]\n";
        $gradingPrompt .= '}';

        $chatParameters = [
            'message' => $gradingPrompt,
            'academic_level' => Auth::user()->academic_level ?? 'high_school',
            'subject' => 'language_arts',
            'topics' => ['essay_grading', 'reading_comprehension'],
            'response_format' => 'json',
            'creativity_level' => 0.3,
            //            'response_length' => 800
        ];

        $result = $this->chatService->chat($chatParameters);

        if ($result['success']) {
            $parsedResult = $this->parseEssayGradingResult($result['content']);

            // FIX: Log the grading result for debugging
            Log::info('Essay grading AI result', [
                'raw_content' => substr($result['content'], 0, 500),
                'parsed_score' => $parsedResult['score'],
                'parsed_feedback' => substr($parsedResult['feedback'], 0, 200),
            ]);

            return $parsedResult;
        }

        // FIX: Improved fallback grading with similarity matching
        $expectedAnswer = $question['correct_answer'] ?? '';
        $similarityScore = $this->calculateTextSimilarity($answer, $expectedAnswer);

        return [
            'score' => $similarityScore,
            'feedback' => 'Your answer shows '.($similarityScore >= 70 ? 'good' : ($similarityScore >= 40 ? 'moderate' : 'limited')).' similarity to the expected answer. Score: '.$similarityScore.'%',
        ];
    }

    protected function parseEssayGradingResult(string $content): array
    {
        // Try to extract JSON from the response
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');

        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);

            try {
                $parsed = json_decode($jsonString, true);

                if (is_array($parsed)) {
                    return [
                        'score' => min(100, max(0, $parsed['score'] ?? 75)),
                        'feedback' => $parsed['feedback'] ?? 'No specific feedback provided.',
                    ];
                }
            } catch (Exception $e) {
                Log::warning('Failed to parse essay grading JSON', [
                    'content' => $content,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Extract score using regex as fallback
        $score = 75; // Default
        if (preg_match('/(?:score|grade):\s*(\d+)/i', $content, $matches)) {
            $score = (int) $matches[1];
        }

        // Extract feedback
        $feedback = $content;
        if (preg_match('/(?:feedback|comments?):\s*(.*?)(?:\n\n|\Z)/si', $content, $matches)) {
            $feedback = trim($matches[1]);
        }

        return [
            'score' => min(100, max(0, $score)),
            'feedback' => $feedback,
        ];
    }

    protected function calculateTextSimilarity(string $text1, string $text2): int
    {
        // Convert to lowercase and remove extra whitespace
        $text1 = strtolower(trim(preg_replace('/\s+/', ' ', $text1)));
        $text2 = strtolower(trim(preg_replace('/\s+/', ' ', $text2)));

        // If either text is empty, return 0
        if (empty($text1) || empty($text2)) {
            return 0;
        }

        // Split into words
        $words1 = explode(' ', $text1);
        $words2 = explode(' ', $text2);

        // Calculate Jaccard similarity (intersection over union)
        $intersection = count(array_intersect($words1, $words2));
        $union = count(array_unique(array_merge($words1, $words2)));

        if ($union == 0) {
            return 0;
        }

        $jaccard = $intersection / $union;

        // Also check if one text is contained in the other
        $containment = 0;
        if (strlen($text1) > 0 && strlen($text2) > 0) {
            if (strpos($text1, $text2) !== false || strpos($text2, $text1) !== false) {
                $containment = min(strlen($text1), strlen($text2)) / max(strlen($text1), strlen($text2));
            }
        }

        // Combine both metrics
        $similarity = ($jaccard * 0.7) + ($containment * 0.3);

        return (int) ($similarity * 100);
    }

    protected function updateReadingProgress(array $results): void
    {
        // Only update reading progress if a book is selected
        if (! $this->selectedBookId) {
            return;
        }

        // Update user's reading progress based on quiz performance
        $progress = BookReadingProgress::where('user_id', Auth::id())
            ->where('book_id', $this->selectedBookId)
            ->first();

        if ($progress) {
            // Update comprehension score based on quiz performance
            $comprehensionScore = $results['percentage'];

            // If this is a better score, update it
            $currentScore = $progress->comprehension_score ?? 0;
            if ($comprehensionScore > $currentScore) {
                $progress->update([
                    'comprehension_score' => $comprehensionScore,
                    'last_read_at' => now(),
                ]);
            }
        }
    }

    public function resetQuiz()
    {
        $this->quizData = null;
        $this->quizResults = null;
        $this->activeTab = 'new';
        $this->errors = [];
        $this->dispatch('quiz-reset');
    }

    public function exportResults()
    {
        if (! $this->quizResults) {
            return;
        }
        $this->showDetailedResultsModal = true;
        // Implement export functionality
        $exportData = [
            'book' => $this->selectedBook->title,
            'author' => $this->selectedBook->author_name,
            'quiz_date' => now()->format('Y-m-d'),
            'results' => $this->quizResults['results'],
            'performance' => $this->quizResults['detailed_feedback'],
        ];

        $this->dispatch('download-results', $exportData);
    }

    public function render()
    {
        return view('livewire.learning.book-quiz-interface');
    }
}
