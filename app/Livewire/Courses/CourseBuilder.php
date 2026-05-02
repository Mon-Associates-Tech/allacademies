<?php

namespace App\Livewire\Courses;

use App\Models\Lms\Course;
use App\Models\Lms\CourseChapter;
use App\Models\Lms\CourseContent;
use App\Models\Lms\CourseSection;
use App\Services\AcademicChatService;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CourseBuilder extends Component
{
    use WithFileUploads;

    public ?Course $course = null;

    public bool $isEditing = false;

    // Course fields
    public string $title = '';

    public string $description = '';

    public string $objectives = '';

    public string $difficulty_level = 'beginner';

    public string $audience = 'public';

    public string $price = '0';

    public bool $is_free = true;

    public ?int $estimated_duration_minutes = null;

    public $thumbnail;

    public ?string $existingThumbnail = null;

    // Chapter management
    public string $newChapterTitle = '';

    public string $newChapterDescription = '';

    public ?int $editingChapterId = null;

    // Section management
    public string $newSectionTitle = '';

    public string $newSectionDescription = '';

    public ?int $selectedChapterId = null;

    public ?int $parentSectionId = null;

    public ?int $editingSectionId = null;

    // Content management
    public string $newContentTitle = '';

    public string $newContentType = 'text';

    public string $newContentBody = '';

    public $newContentMedia;

    public ?int $selectedSectionId = null;

    public ?int $editingContentId = null;

    // Quiz content fields
    public string $quizSource = 'manual'; // 'ai', 'manual', 'both'

    public string $quizQuestionType = 'multiple_choice'; // 'multiple_choice', 'essay', 'true_false'

    public int $quizQuestionCount = 5;

    public string $quizSectionContext = ''; // section content for AI context

    public array $quizQuestions = []; // manually entered or AI-generated questions

    public bool $isGeneratingQuiz = false;

    public string $quizGenerationError = '';

    // Feedback content fields
    public string $feedbackPrompt = ''; // the textarea prompt shown to course takers

    // UI state
    public string $activeTab = 'details';

    public int $currentStep = 1;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'objectives' => 'nullable|string',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced',
        'audience' => 'required|in:public,school_only',
        'price' => 'required|numeric|min:0',
        'is_free' => 'boolean',
        'estimated_duration_minutes' => 'nullable|integer|min:1',
        'thumbnail' => 'nullable|image|max:20480',
        'quizQuestionCount' => 'integer|min:1|max:50',
        'feedbackPrompt' => 'nullable|string|max:1000',
    ];

    public function mount(?Course $course = null): void
    {
        if ($course && $course->exists) {
            $this->course = $course;
            $this->isEditing = true;
            $this->fillFromCourse();
        }
    }

    protected function fillFromCourse(): void
    {
        $this->title = $this->course->title;
        $this->description = $this->course->description ?? '';
        $this->objectives = $this->course->objectives ?? '';
        $this->difficulty_level = $this->course->difficulty_level;
        $this->audience = $this->course->audience;
        $this->price = (string) $this->course->price;
        $this->is_free = $this->course->is_free;
        $this->estimated_duration_minutes = $this->course->estimated_duration_minutes;
        $this->existingThumbnail = $this->course->thumbnail;
    }

    /**
     * Get the thumbnail preview URL.
     * Uses base64 encoding to avoid issues with Livewire's temporaryUrl() signed URLs.
     */
    public function getThumbnailPreviewUrl(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

        try {
            $path = $this->thumbnail->getRealPath();
            if ($path && file_exists($path)) {
                $mimeType = $this->thumbnail->getMimeType() ?? 'image/jpeg';
                $contents = file_get_contents($path);

                return 'data:'.$mimeType.';base64,'.base64_encode($contents);
            }
        } catch (\Exception $e) {
            // Fall back to temporaryUrl if base64 fails
            try {
                return $this->thumbnail->temporaryUrl();
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= 4) {
            $this->currentStep = $step;
        }
    }

    public function nextStep(): void
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function saveCourse(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->isEditing ? $this->course->slug : Str::slug($this->title).'-'.Str::random(6),
            'description' => $this->description,
            'objectives' => $this->objectives,
            'difficulty_level' => $this->difficulty_level,
            'audience' => $this->audience,
            'price' => $this->is_free ? 0 : $this->price,
            'is_free' => $this->is_free,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
        ];

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('course-thumbnails', 'public');
            // Update existingThumbnail and reset the upload
            $this->existingThumbnail = $data['thumbnail'];
            $this->thumbnail = null;
        }

        if ($this->isEditing) {
            $this->course->update($data);
            session()->flash('message', 'Course updated successfully.');
        } else {
            $data['created_by'] = auth()->id();
            $data['school_id'] = auth()->user()->school_id;
            $data['status'] = 'draft';

            $this->course = Course::create($data);
            $this->isEditing = true;
            session()->flash('message', 'Course created successfully.');
        }

        $this->activeTab = 'chapters';
    }

    public function addChapter(): void
    {
        $this->validate([
            'newChapterTitle' => 'required|string|max:255',
        ]);

        // Ensure course exists before adding chapters
        if (! $this->course || ! $this->course->exists) {
            // Auto-save the course first
            $this->validate([
                'title' => 'required|string|max:255',
            ]);

            $courseData = [
                'title' => $this->title,
                'slug' => Str::slug($this->title).'-'.Str::random(6),
                'description' => $this->description,
                'objectives' => $this->objectives,
                'difficulty_level' => $this->difficulty_level,
                'audience' => $this->audience,
                'price' => $this->is_free ? 0 : $this->price,
                'is_free' => $this->is_free,
                'estimated_duration_minutes' => $this->estimated_duration_minutes,
                'created_by' => auth()->id(),
                'school_id' => auth()->user()->school_id,
                'status' => 'draft',
            ];

            // Include thumbnail if uploaded
            if ($this->thumbnail) {
                $courseData['thumbnail'] = $this->thumbnail->store('course-thumbnails', 'public');
                $this->existingThumbnail = $courseData['thumbnail'];
                $this->thumbnail = null;
            }

            $this->course = Course::create($courseData);
            $this->isEditing = true;
        }

        $maxOrder = $this->course->chapters()->max('order') ?? 0;

        $this->course->chapters()->create([
            'title' => $this->newChapterTitle,
            'description' => $this->newChapterDescription,
            'order' => $maxOrder + 1,
            'is_published' => true,
        ]);

        $this->newChapterTitle = '';
        $this->newChapterDescription = '';

        session()->flash('message', 'Chapter added successfully.');
    }

    public function updateChapter(int $chapterId): void
    {
        $chapter = CourseChapter::findOrFail($chapterId);

        $this->validate([
            'newChapterTitle' => 'required|string|max:255',
        ]);

        $chapter->update([
            'title' => $this->newChapterTitle,
            'description' => $this->newChapterDescription,
        ]);

        $this->editingChapterId = null;
        $this->newChapterTitle = '';
        $this->newChapterDescription = '';

        session()->flash('message', 'Chapter updated successfully.');
    }

    public function editChapter(int $chapterId): void
    {
        $chapter = CourseChapter::findOrFail($chapterId);
        $this->editingChapterId = $chapterId;
        $this->newChapterTitle = $chapter->title;
        $this->newChapterDescription = $chapter->description ?? '';
    }

    public function deleteChapter(int $chapterId): void
    {
        CourseChapter::findOrFail($chapterId)->delete();
        session()->flash('message', 'Chapter deleted successfully.');
    }

    public function reorderChapter(int $chapterId, string $direction): void
    {
        if (! $this->course || ! $this->course->exists) {
            return;
        }

        $chapter = CourseChapter::findOrFail($chapterId);
        $chapters = $this->course->chapters()->orderBy('order')->get();

        $currentIndex = $chapters->search(fn ($c) => $c->id === $chapterId);

        if ($direction === 'up' && $currentIndex > 0) {
            $swapWith = $chapters[$currentIndex - 1];
            $tempOrder = $chapter->order;
            $chapter->update(['order' => $swapWith->order]);
            $swapWith->update(['order' => $tempOrder]);
        } elseif ($direction === 'down' && $currentIndex < $chapters->count() - 1) {
            $swapWith = $chapters[$currentIndex + 1];
            $tempOrder = $chapter->order;
            $chapter->update(['order' => $swapWith->order]);
            $swapWith->update(['order' => $tempOrder]);
        }
    }

    public function selectChapter(int $chapterId): void
    {
        $this->selectedChapterId = $chapterId;
        $this->selectedSectionId = null;
        $this->activeTab = 'sections';
    }

    public function addSection(): void
    {
        $this->validate([
            'newSectionTitle' => 'required|string|max:255',
        ]);

        $chapter = CourseChapter::findOrFail($this->selectedChapterId);
        $maxOrder = $chapter->sections()->whereNull('parent_section_id')->max('order') ?? 0;

        $chapter->sections()->create([
            'title' => $this->newSectionTitle,
            'description' => $this->newSectionDescription,
            'parent_section_id' => $this->parentSectionId,
            'order' => $maxOrder + 1,
            'is_published' => true,
        ]);

        $this->newSectionTitle = '';
        $this->newSectionDescription = '';
        $this->parentSectionId = null;

        session()->flash('message', 'Section added successfully.');
    }

    public function deleteSection(int $sectionId): void
    {
        CourseSection::findOrFail($sectionId)->delete();
        session()->flash('message', 'Section deleted successfully.');
    }

    public function selectSection(int $sectionId): void
    {
        $this->selectedSectionId = $sectionId;
        $this->activeTab = 'content';
    }

    public function addContent(): void
    {
        $this->validate([
            'newContentTitle' => 'required|string|max:255',
            'newContentType' => 'required|in:video,audio,text,quiz,feedback',
        ]);

        $section = CourseSection::findOrFail($this->selectedSectionId);
        $maxOrder = $section->contents()->max('order') ?? 0;

        $contentData = [
            'title' => $this->newContentTitle,
            'type' => $this->newContentType,
            'order' => $maxOrder + 1,
            'is_required' => true,
            'is_published' => true,
        ];

        if ($this->newContentType === 'text') {
            $contentData['content'] = $this->newContentBody;
            $contentData['word_count'] = str_word_count($this->newContentBody);
        }

        if ($this->newContentMedia && in_array($this->newContentType, ['video', 'audio'])) {
            $contentData['media_path'] = $this->newContentMedia->store('course-media', 'public');
        }

        if ($this->newContentType === 'quiz') {
            if (empty($this->quizQuestions)) {
                $this->addError('quizQuestions', 'Please add at least one question.');

                return;
            }

            $contentData['content'] = json_encode([
                'questions' => $this->quizQuestions,
                'question_type' => $this->quizQuestionType,
                'source' => $this->quizSource,
            ]);
        }

        if ($this->newContentType === 'feedback') {
            $this->validate(['feedbackPrompt' => 'required|string|min:5']);
            $contentData['content'] = json_encode(['prompt' => $this->feedbackPrompt]);
        }

        $section->contents()->create($contentData);

        $this->resetContentForm();
        session()->flash('message', 'Content added successfully.');
    }

    public function deleteContent(int $contentId): void
    {
        CourseContent::findOrFail($contentId)->delete();
        session()->flash('message', 'Content deleted successfully.');
    }

    protected function resetContentForm(): void
    {
        $this->newContentTitle = '';
        $this->newContentType = 'text';
        $this->newContentBody = '';
        $this->newContentMedia = null;
        $this->quizSource = 'manual';
        $this->quizQuestionType = 'multiple_choice';
        $this->quizQuestionCount = 5;
        $this->quizSectionContext = '';
        $this->quizQuestions = [];
        $this->feedbackPrompt = '';
        $this->quizGenerationError = '';
    }

    public function generateQuizQuestions(AcademicChatService $chatService): void
    {
        $this->quizGenerationError = '';

        if (! auth()->user()?->hasOpenAiTokens()) {
            $this->quizGenerationError = 'You need an active AI token subscription to generate questions.';

            return;
        }

        $this->validate([
            'quizQuestionCount' => 'required|integer|min:1|max:50',
            'quizQuestionType' => 'required|in:multiple_choice,essay,true_false',
        ]);

        $this->isGeneratingQuiz = true;

        $section = CourseSection::find($this->selectedSectionId);
        $context = $this->quizSectionContext ?: ($section?->title ?? 'the course section');

        $questionCount = $this->quizQuestionCount;
        $questionType = $this->quizQuestionType;

        $prompt = "Generate a quiz based on the following course section content.\n\n";
        $prompt .= "SECTION CONTEXT:\n{$context}\n\n";
        $prompt .= "QUIZ REQUIREMENTS:\n";
        $prompt .= "⚠️ CRITICAL: Generate EXACTLY {$questionCount} complete questions.\n";
        $prompt .= "- Total Questions: {$questionCount}\n";
        $prompt .= "- Question Type: {$questionType}\n\n";
        $prompt .= "=== RESPONSE FORMAT (JSON ONLY) ===\n";
        $prompt .= "Return ONLY valid JSON:\n{\n  \"questions\": [\n";

        if ($questionType === 'multiple_choice') {
            $prompt .= '    {"question": "...", "type": "multiple_choice", "options": ["A", "B", "C", "D"], "correct_answer": "A", "explanation": "...", "points": 1}'."\n";
        } elseif ($questionType === 'true_false') {
            $prompt .= '    {"question": "...", "type": "true_false", "options": ["True", "False"], "correct_answer": "True", "explanation": "...", "points": 1}'."\n";
        } else {
            $prompt .= '    {"question": "...", "type": "essay", "correct_answer": "Expected answer...", "explanation": "...", "points": 3}'."\n";
        }

        $prompt .= "  ]\n}\n\n";
        $prompt .= "RULES:\n";
        $prompt .= "1. Generate ALL {$questionCount} questions\n";
        $prompt .= "2. For multiple_choice: exactly 4 options\n";
        $prompt .= "3. For true_false: options are [\"True\", \"False\"]\n";
        $prompt .= "4. For essay: no options field\n";
        $prompt .= "5. Return ONLY JSON, no markdown\n";

        $result = $chatService->chat([
            'input' => $prompt,
            'request_type' => 'quiz_generation',
            'creativity_level' => 0.7,
            'response_length' => $questionCount * 500 + 500,
        ]);

        $this->isGeneratingQuiz = false;

        if (! $result['success']) {
            $this->quizGenerationError = 'Failed to generate questions: '.($result['error'] ?? 'Unknown error');

            return;
        }

        $parsed = $this->parseGeneratedQuestions($result['content']);

        if (empty($parsed)) {
            $this->quizGenerationError = 'Could not parse generated questions. Please try again.';

            return;
        }

        if ($this->quizSource === 'ai') {
            $this->quizQuestions = $parsed;
        } else {
            // 'both' — append AI questions to existing manual ones
            $this->quizQuestions = array_merge($this->quizQuestions, $parsed);
        }

        session()->flash('message', count($parsed).' questions generated successfully.');
    }

    protected function parseGeneratedQuestions(string $content): array
    {
        $content = preg_replace('/```(?:json)?\s*/', '', $content);
        $content = trim($content);

        $firstBrace = strpos($content, '{');
        $lastBrace = strrpos($content, '}');

        if ($firstBrace !== false && $lastBrace !== false) {
            $json = substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
            $parsed = json_decode($json, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($parsed['questions'])) {
                return $parsed['questions'];
            }
        }

        return [];
    }

    public function addQuizQuestion(): void
    {
        $this->quizQuestions[] = [
            'question' => '',
            'type' => $this->quizQuestionType,
            'options' => $this->quizQuestionType !== 'essay' ? ['', '', '', ''] : [],
            'correct_answer' => '',
            'explanation' => '',
            'points' => $this->quizQuestionType === 'essay' ? 3 : 1,
        ];
    }

    public function removeQuizQuestion(int $index): void
    {
        array_splice($this->quizQuestions, $index, 1);
        $this->quizQuestions = array_values($this->quizQuestions);
    }

    public function publishCourse(): void
    {
        $this->course->publish();
        session()->flash('message', 'Course published successfully.');
    }

    public function unpublishCourse(): void
    {
        $this->course->unpublish();
        session()->flash('message', 'Course unpublished.');
    }

    public function render()
    {
        $chapters = $this->course?->chapters()->orderBy('order')->get() ?? collect();
        $selectedChapter = $this->selectedChapterId ? CourseChapter::find($this->selectedChapterId) : null;
        $selectedSection = $this->selectedSectionId ? CourseSection::find($this->selectedSectionId) : null;

        return view('livewire.courses.course-builder', [
            'chapters' => $chapters,
            'selectedChapter' => $selectedChapter,
            'selectedSection' => $selectedSection,
            'sections' => $selectedChapter?->sections()->orderBy('order')->get() ?? collect(),
            'contents' => $selectedSection?->contents()->orderBy('order')->get() ?? collect(),
        ]);
    }
}
