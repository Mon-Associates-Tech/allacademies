<?php

namespace App\Livewire\Books;

use App\Enums\PublishingStatus;
use App\Enums\UserRole;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookForm extends Component
{
    use WithFileUploads;

    // Step management
    public int $currentStep = 1;

    public int $totalSteps = 5;

    // Mode
    public string $mode = 'create';

    public ?Book $book = null;

    // Step 1: Basic Info
    public $title;

    public $slug;

    public $authorId;

    public $bookCategoryIds = [];

    public $ageGroups = [];

    public $academicGroupIds = [];

    public $academicLevelIds = [];

    public $academicSubjectIds = [];

    public $showNewAuthorForm = false;

    public $newAuthorName;

    public $showNewCategoryForm = false;

    public $newCategoryName;

    public $newCategoryDescription;

    // Step 2: Book Details
    public $edition;

    public $publisher;

    public $pages;

    public $hasHardcopy = false;

    public $hasSoftcopy = false;

    public $annualSubscriptionFee = 0;

    public $additionalInfo;

    // Step 3: Table of Contents
    public $showTableOfContents = false;

    public $tableOfContents = [];

    public $expandedChapters = [];

    // Step 4: Media Files
    public $coverImage;

    public $pdfFile;

    public $samplePdfFile;

    public $singleAudio;

    public $singleVideo;

    public $chapterAudios = [];

    public $chapterVideos = [];

    public $newChapterAudios;

    public $newChapterVideos;

    public $selectedChapterForAudio = null;

    public $selectedChapterForVideo = null;

    public $existingCoverImage;

    public $existingPdfFile;

    public $existingSamplePdfFile;

    public $existingSingleAudio;

    public $existingSingleVideo;

    public $existingChapterAudios = [];

    public $existingChapterVideos = [];

    public $removeSamplePdfFile = false;

    public $removePdfFile = false;

    public $removeCoverImage = false;

    public $removeSingleAudioFile = false;

    public $removeSingleVideoFile = false;

    // Audio/Video toggles
    public bool $hasAudio = false;

    public bool $hasVideo = false;

    // Track when files have been uploaded and stored
    public bool $pdfFileUploaded = false;

    public bool $coverImageUploaded = false;

    public bool $samplePdfFileUploaded = false;

    public bool $singleAudioUploaded = false;

    public bool $singleVideoUploaded = false;

    // Step 5: Publishing
    public $status = 'draft';

    // Data collections
    public $authors;

    public $bookCategories;

    public $academicGroups;

    public $academicLevels;

    public $academicSubjects;

    protected $listeners = [
        'selection-changed' => 'handleSelectionChanged',
    ];

    public function mount(?Book $book = null): void
    {
        if ($book && $book->exists) {
            $this->mode = 'edit';
            $this->book = $book;
            $this->authorizeEdit();
            $this->loadBookData();
        }

        // Restore form data from session if available (survives page reloads)
        $this->restoreFromSession();

        $this->loadData();
    }

    /**
     * Save current form data to session for persistence across page reloads.
     */
    public function saveToSession(): void
    {
        $sessionKey = $this->book
            ? "book_form_{$this->book->id}"
            : 'book_form_create';

        session()->put($sessionKey, [
            'currentStep' => $this->currentStep,
            'title' => $this->title,
            'slug' => $this->slug,
            'authorId' => $this->authorId,
            'bookCategoryIds' => $this->bookCategoryIds,
            'ageGroups' => $this->ageGroups,
            'academicGroupIds' => $this->academicGroupIds,
            'academicLevelIds' => $this->academicLevelIds,
            'academicSubjectIds' => $this->academicSubjectIds,
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'hasHardcopy' => $this->hasHardcopy,
            'hasSoftcopy' => $this->hasSoftcopy,
            'annualSubscriptionFee' => $this->annualSubscriptionFee,
            'additionalInfo' => $this->additionalInfo,
            'tableOfContents' => $this->tableOfContents,
            'status' => $this->status,
            'hasAudio' => $this->hasAudio,
            'hasVideo' => $this->hasVideo,
            'pdfFileUploaded' => $this->pdfFileUploaded,
            'coverImageUploaded' => $this->coverImageUploaded,
            'samplePdfFileUploaded' => $this->samplePdfFileUploaded,
            'singleAudioUploaded' => $this->singleAudioUploaded,
            'singleVideoUploaded' => $this->singleVideoUploaded,
            'existingPdfFile' => $this->existingPdfFile,
            'existingCoverImage' => $this->existingCoverImage,
            'existingSamplePdfFile' => $this->existingSamplePdfFile,
            'existingSingleAudio' => $this->existingSingleAudio,
            'existingSingleVideo' => $this->existingSingleVideo,
            'chapterAudios' => $this->chapterAudios,
            'chapterVideos' => $this->chapterVideos,
            'removePdfFile' => $this->removePdfFile,
            'removeCoverImage' => $this->removeCoverImage,
            'removeSamplePdfFile' => $this->removeSamplePdfFile,
            'removeSingleAudioFile' => $this->removeSingleAudioFile,
            'removeSingleVideoFile' => $this->removeSingleVideoFile,
        ]);
    }

    /**
     * Restore form data from session if available.
     */
    public function restoreFromSession(): void
    {
        $sessionKey = $this->book
            ? "book_form_{$this->book->id}"
            : 'book_form_create';

        $data = session()->get($sessionKey);

        if (!$data) {
            return;
        }

        // Only restore if not already loaded from book (edit mode)
        if ($this->book && $this->book->exists) {
            return;
        }

        $this->currentStep = $data['currentStep'] ?? 1;
        $this->title = $data['title'] ?? null;
        $this->slug = $data['slug'] ?? null;
        $this->authorId = $data['authorId'] ?? null;
        $this->bookCategoryIds = $data['bookCategoryIds'] ?? [];
        $this->ageGroups = $data['ageGroups'] ?? [];
        $this->academicGroupIds = $data['academicGroupIds'] ?? [];
        $this->academicLevelIds = $data['academicLevelIds'] ?? [];
        $this->academicSubjectIds = $data['academicSubjectIds'] ?? [];
        $this->edition = $data['edition'] ?? null;
        $this->publisher = $data['publisher'] ?? null;
        $this->pages = $data['pages'] ?? null;
        $this->hasHardcopy = $data['hasHardcopy'] ?? false;
        $this->hasSoftcopy = $data['hasSoftcopy'] ?? false;
        $this->annualSubscriptionFee = $data['annualSubscriptionFee'] ?? 0;
        $this->additionalInfo = $data['additionalInfo'] ?? null;
        $this->tableOfContents = $data['tableOfContents'] ?? [];
        $this->status = $data['status'] ?? 'draft';
        $this->hasAudio = $data['hasAudio'] ?? false;
        $this->hasVideo = $data['hasVideo'] ?? false;
        $this->pdfFileUploaded = $data['pdfFileUploaded'] ?? false;
        $this->coverImageUploaded = $data['coverImageUploaded'] ?? false;
        $this->samplePdfFileUploaded = $data['samplePdfFileUploaded'] ?? false;
        $this->singleAudioUploaded = $data['singleAudioUploaded'] ?? false;
        $this->singleVideoUploaded = $data['singleVideoUploaded'] ?? false;
        $this->existingPdfFile = $data['existingPdfFile'] ?? null;
        $this->existingCoverImage = $data['existingCoverImage'] ?? null;
        $this->existingSamplePdfFile = $data['existingSamplePdfFile'] ?? null;
        $this->existingSingleAudio = $data['existingSingleAudio'] ?? null;
        $this->existingSingleVideo = $data['existingSingleVideo'] ?? null;
        $this->chapterAudios = $data['chapterAudios'] ?? [];
        $this->chapterVideos = $data['chapterVideos'] ?? [];
        $this->removePdfFile = $data['removePdfFile'] ?? false;
        $this->removeCoverImage = $data['removeCoverImage'] ?? false;
        $this->removeSamplePdfFile = $data['removeSamplePdfFile'] ?? false;
        $this->removeSingleAudioFile = $data['removeSingleAudioFile'] ?? false;
        $this->removeSingleVideoFile = $data['removeSingleVideoFile'] ?? false;
    }

    /**
     * Clear session data for this form.
     */
    public function clearSession(): void
    {
        $sessionKey = $this->book
            ? "book_form_{$this->book->id}"
            : 'book_form_create';

        session()->forget($sessionKey);
    }

    private function authorizeEdit(): void
    {
        $user = auth()->user();

        if ($user->role === UserRole::SUPER_ADMIN || $user->role === UserRole::OWNER || $user->role === UserRole::ADMIN) {
            return;
        }

        if ($user->role === UserRole::AUTHOR && $this->book->author?->user_id === $user->id) {
            return;
        }

        if ($user->role === UserRole::TEACHER) {
            return;
        }

        abort(403, 'You are not authorized to edit this book.');
    }

    private function loadBookData(): void
    {
        $this->title = $this->book->title;
        $this->slug = $this->book->slug;
        $this->authorId = $this->book->author_id;
        $this->bookCategoryIds = $this->book->categories->pluck('id')->toArray() ?: [$this->book->book_category_id];
        $this->ageGroups = $this->book->age_groups ?? [];
        $this->academicGroupIds = $this->book->academic_group_ids ?? [];
        $this->academicLevelIds = $this->book->academic_level_ids ?? [];
        $this->academicSubjectIds = $this->book->academic_subject_ids ?? [];
        $this->edition = $this->book->edition;
        $this->publisher = $this->book->publisher;
        $this->pages = $this->book->pages;
        $this->hasHardcopy = $this->book->has_hardcopy;
        $this->hasSoftcopy = $this->book->has_softcopy;
        $this->annualSubscriptionFee = $this->book->annual_subscription_fee;
        $this->additionalInfo = $this->book->additional_info;
        $this->tableOfContents = $this->book->table_of_contents ?? [];
        $this->status = $this->book->status ?? 'draft';
        $this->existingCoverImage = $this->book->cover_image;
        $this->existingPdfFile = $this->book->content_url;
        $this->existingSamplePdfFile = $this->book->sample_url;
        $this->existingSingleAudio = $this->book->single_audio;
        $this->existingSingleVideo = $this->book->single_video;
        $this->existingChapterAudios = $this->book->chapter_audios ?? [];
        $this->existingChapterVideos = $this->book->chapter_videos ?? [];
        $this->chapterAudios = $this->existingChapterAudios;
        $this->chapterVideos = $this->existingChapterVideos;
    }

    private function loadData(): void
    {
        $this->authors = Author::orderBy('name')->get();
        $this->bookCategories = BookCategory::orderBy('name')->get();
        $this->academicGroups = AcademicGroup::orderBy('name')->get();
        $this->academicLevels = AcademicLevel::orderBy('name')->get();
        $this->academicSubjects = AcademicSubject::orderBy('name')->get();
    }

    public function handleSelectionChanged($data): void
    {
        $name = $data['name'] ?? null;
        $selected = $data['selected'] ?? [];

        match ($name) {
            'authorId' => $this->authorId = $selected[0] ?? null,
            'bookCategoryIds' => $this->bookCategoryIds = $selected,
            'ageGroups' => $this->ageGroups = $selected,
            'academicGroupIds' => $this->academicGroupIds = $selected,
            'academicLevelIds' => $this->academicLevelIds = $selected,
            'academicSubjectIds' => $this->academicSubjectIds = $selected,
            default => null,
        };
    }

    public function updatedTitle(): void
    {
        if ($this->mode === 'create') {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedPages(): void
    {
        if ($this->pages && empty($this->tableOfContents)) {
            $this->generateTableOfContents();
        }
    }

    // File upload hooks — store immediately and set flags so "Processing..." spinner disappears
    public function updatedPdfFile(): void
    {
        if ($this->pdfFile) {
            $path = $this->pdfFile->store('book-pdfs', 'public');
            $this->existingPdfFile = $path;
            $this->pdfFileUploaded = true;
            $this->reset('pdfFile');
        }
    }

    public function updatedCoverImage(): void
    {
        if ($this->coverImage) {
            $path = $this->coverImage->store('book-covers', 'public');
            $this->existingCoverImage = $path;
            $this->coverImageUploaded = true;
            $this->reset('coverImage');
        }
    }

    public function updatedSamplePdfFile(): void
    {
        if ($this->samplePdfFile) {
            $path = $this->samplePdfFile->store('book-samples', 'public');
            $this->existingSamplePdfFile = $path;
            $this->samplePdfFileUploaded = true;
            $this->reset('samplePdfFile');
        }
    }

    public function updatedSingleAudio(): void
    {
        if ($this->singleAudio) {
            $path = $this->singleAudio->store('book-audios', 'public');
            $this->existingSingleAudio = $path;
            $this->singleAudioUploaded = true;
            $this->reset('singleAudio');
        }
    }

    public function updatedSingleVideo(): void
    {
        if ($this->singleVideo) {
            $path = $this->singleVideo->store('book-videos', 'public');
            $this->existingSingleVideo = $path;
            $this->singleVideoUploaded = true;
            $this->reset('singleVideo');
        }
    }

    public function generateTableOfContents(): void
    {
        if (!$this->pages) {
            return;
        }

        $chaptersCount = max(1, min(15, intval($this->pages / 20)));
        $this->tableOfContents = [];

        for ($i = 1; $i <= $chaptersCount; $i++) {
            $this->tableOfContents[] = [
                'chapter' => $i,
                'title' => "Chapter {$i}",
                'description' => "Content for chapter {$i}",
                'page_start' => (($i - 1) * intval($this->pages / $chaptersCount)) + 1,
                'page_end' => $i * intval($this->pages / $chaptersCount),
                'sections' => [],
            ];
        }
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->saveToSession();
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->saveToSession();
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            // Validate all steps before the target step
            for ($i = 1; $i < $step; $i++) {
                $this->validateStep($i);
            }
            $this->currentStep = $step;
            $this->saveToSession();
        }
    }

    private function validateCurrentStep(): void
    {
        $this->validateStep($this->currentStep);
    }

    private function validateStep(int $step): void
    {
        $rules = match ($step) {
            1 => [
                'title' => 'required|min:3|max:255',
                'authorId' => 'required|exists:authors,id',
                'bookCategoryIds' => 'required|array|min:1',
            ],
            2 => [
                'pages' => 'nullable|integer|min:1|max:9999',
                'annualSubscriptionFee' => 'nullable|numeric|min:0|max:999999.99',
            ],
            4 => [
                'coverImage' => 'nullable|image|max:10240',
            ],
            default => [],
        };

        if (!empty($rules)) {
            $this->validate($rules);
        }
    }

    public function updatedNewChapterAudios($value)
    {
        if (!$this->selectedChapterForAudio || empty($value)) {
            return;
        }

        $chapter = collect($this->tableOfContents)->firstWhere('chapter', $this->selectedChapterForAudio);

        foreach ($value as $audio) {
            $filePath = $audio->store('book-chapter-audios', 'public');

            $this->chapterAudios[] = [
                'chapter' => $this->selectedChapterForAudio,
                'file' => $filePath,
                'title' => $chapter['title'] ?? "Chapter {$this->selectedChapterForAudio}",
                'description' => $chapter['description'] ?? '',
            ];
        }

        $this->reset('selectedChapterForAudio');
    }

    public function updatedNewChapterVideos($value)
    {
        if (!$this->selectedChapterForVideo || empty($value)) {
            return;
        }

        $chapter = collect($this->tableOfContents)->firstWhere('chapter', $this->selectedChapterForVideo);

        foreach ($value as $video) {
            $filePath = $video->store('book-chapter-videos', 'public');

            $this->chapterVideos[] = [
                'chapter' => $this->selectedChapterForVideo,
                'file' => $filePath,
                'title' => $chapter['title'] ?? "Chapter {$this->selectedChapterForVideo}",
                'description' => $chapter['description'] ?? '',
            ];
        }

        $this->reset('selectedChapterForVideo');
    }

    public function toggleNewAuthorForm(): void
    {
        $this->showNewAuthorForm = !$this->showNewAuthorForm;
        $this->reset(['newAuthorName']);
        $this->resetValidation(['newAuthorName']);
    }

    public function createNewAuthor(): void
    {
        $this->validate(['newAuthorName' => 'required|string|max:255']);

        $author = Author::create(['name' => $this->newAuthorName]);
        $this->loadData();
        $this->authorId = $author->id;
        $this->showNewAuthorForm = false;
        $this->reset(['newAuthorName']);
        session()->flash('message', 'New author created successfully!');
    }

    public function toggleNewCategoryForm(): void
    {
        $this->showNewCategoryForm = !$this->showNewCategoryForm;
        $this->reset(['newCategoryName', 'newCategoryDescription']);
        $this->resetValidation(['newCategoryName', 'newCategoryDescription']);
    }

    public function createNewCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:book_categories,name',
            'newCategoryDescription' => 'nullable|string',
        ]);

        $category = BookCategory::create([
            'name' => $this->newCategoryName,
            'description' => $this->newCategoryDescription,
        ]);

        $this->loadData();
        $this->bookCategoryIds[] = $category->id;
        $this->showNewCategoryForm = false;
        $this->reset(['newCategoryName', 'newCategoryDescription']);
        session()->flash('message', 'New category created successfully!');
    }

    public function toggleChapter($index): void
    {
        if (in_array($index, $this->expandedChapters)) {
            $this->expandedChapters = array_diff($this->expandedChapters, [$index]);
        } else {
            $this->expandedChapters[] = $index;
        }
    }

    public function addChapter(): void
    {
        $lastChapter = end($this->tableOfContents);
        $nextChapterNumber = $lastChapter ? $lastChapter['chapter'] + 1 : 1;
        $nextPageStart = $lastChapter ? $lastChapter['page_end'] + 1 : 1;

        $this->tableOfContents[] = [
            'chapter' => $nextChapterNumber,
            'title' => "Chapter {$nextChapterNumber}",
            'description' => '',
            'page_start' => $nextPageStart,
            'page_end' => $nextPageStart + 10,
            'sections' => [],
        ];
    }

    public function removeChapter($index): void
    {
        if (count($this->tableOfContents) > 1) {
            unset($this->tableOfContents[$index]);
            $this->tableOfContents = array_values($this->tableOfContents);
        }
    }

    public function submit(): void
    {
        // Validate all steps
        for ($i = 1; $i <= $this->totalSteps; $i++) {
            $this->validateStep($i);
        }

        if (!$this->hasHardcopy && !$this->hasSoftcopy) {
            $this->addError('hasHardcopy', 'Please select at least one format (hardcopy or softcopy).');
            $this->currentStep = 2;

            return;
        }

        if ($this->hasSoftcopy && !$this->pdfFile && !$this->existingPdfFile) {
            $this->addError('pdfFile', 'PDF file is required for softcopy books.');
            $this->currentStep = 4;

            return;
        }

        DB::transaction(function () {
            $coverPath = $this->handleCoverUpload();
            $pdfPath = $this->handlePdfUpload();
            $samplePdfPath = $this->handleSamplePdfUpload();
            $singleAudioPath = $this->handleSingleAudioUpload();
            $singleVideoPath = $this->handleSingleVideoUpload();
            $chapterAudiosPaths = $this->handleChapterAudiosUpload();
            $chapterVideosPaths = $this->handleChapterVideosUpload();

            $bookData = [
                'title' => $this->title,
                'slug' => $this->slug ?: Str::slug($this->title),
                'author_id' => $this->authorId,
                'book_category_id' => $this->bookCategoryIds[0] ?? null,
                'edition' => $this->edition,
                'publisher' => $this->publisher,
                'pages' => $this->pages,
                'has_hardcopy' => $this->hasHardcopy,
                'has_softcopy' => $this->hasSoftcopy,
                'additional_info' => $this->additionalInfo,
                'annual_subscription_fee' => $this->annualSubscriptionFee ?: 0,
                'status' => $this->status,
                'table_of_contents' => $this->tableOfContents,
                'age_groups' => $this->ageGroups,
                'academic_group_ids' => $this->academicGroupIds,
                'academic_level_ids' => $this->academicLevelIds,
                'academic_subject_ids' => $this->academicSubjectIds,
            ];

            if ($coverPath !== null) {
                $bookData['cover_image'] = $coverPath;
            }

            if ($pdfPath !== null) {
                $bookData['content_url'] = $pdfPath;
            }

            if ($samplePdfPath !== null) {
                $bookData['sample_url'] = $samplePdfPath;
            } elseif ($this->removeSamplePdfFile) {
                $bookData['sample_url'] = null;
            }

            if ($singleAudioPath !== null) {
                $bookData['single_audio'] = $singleAudioPath;
            }

            if ($singleVideoPath !== null) {
                $bookData['single_video'] = $singleVideoPath;
            }

            if ($chapterAudiosPaths !== null) {
                $bookData['chapter_audios'] = $chapterAudiosPaths;
            }

            if ($chapterVideosPaths !== null) {
                $bookData['chapter_videos'] = $chapterVideosPaths;
            }

            if ($this->book) {
                $this->book->update($bookData);
                $this->book->categories()->sync($this->bookCategoryIds);
                $message = 'Book updated successfully!';
            } else {
                $book = Book::create($bookData);
                $book->categories()->sync($this->bookCategoryIds);
                $message = 'Book created successfully!';
            }

            session()->flash('success', $message);
        });

        // Clear session data after successful submission
        $this->clearSession();
        $this->cancel();
    }

    private function handleCoverUpload(): ?string
    {
        if ($this->coverImage) {
            if ($this->book && $this->existingCoverImage && Storage::disk('public')->exists($this->existingCoverImage)) {
                Storage::disk('public')->delete($this->existingCoverImage);
            }

            $path = $this->coverImage->store('book-covers', 'public');
            $this->existingCoverImage = $path;
            $this->coverImageUploaded = true;
            return $path;
        }

        if ($this->coverImageUploaded) {
            return $this->existingCoverImage;
        }

        return $this->book ? $this->existingCoverImage : null;
    }

    private function handlePdfUpload(): ?string
    {
        if ($this->pdfFile) {
            if ($this->book && $this->existingPdfFile && Storage::disk('public')->exists($this->existingPdfFile)) {
                Storage::disk('public')->delete($this->existingPdfFile);
            }

            $path = $this->pdfFile->store('book-pdfs', 'public');
            $this->existingPdfFile = $path;
            $this->pdfFileUploaded = true;
            return $path;
        }

        if ($this->pdfFileUploaded) {
            return $this->existingPdfFile;
        }

        return $this->book ? $this->existingPdfFile : null;
    }

    private function handleSamplePdfUpload(): ?string
    {
        if ($this->samplePdfFile) {
            if ($this->book && $this->existingSamplePdfFile && Storage::disk('public')->exists($this->existingSamplePdfFile)) {
                Storage::disk('public')->delete($this->existingSamplePdfFile);
            }

            $path = $this->samplePdfFile->store('book-samples', 'public');
            $this->existingSamplePdfFile = $path;
            $this->samplePdfFileUploaded = true;
            return $path;
        }

        if ($this->samplePdfFileUploaded) {
            return $this->existingSamplePdfFile;
        }

        return $this->book ? $this->existingSamplePdfFile : null;
    }

    private function handleSingleAudioUpload(): ?string
    {
        if ($this->singleAudio) {
            if ($this->book && $this->existingSingleAudio && Storage::disk('public')->exists($this->existingSingleAudio)) {
                Storage::disk('public')->delete($this->existingSingleAudio);
            }

            $path = $this->singleAudio->store('book-audios', 'public');
            $this->existingSingleAudio = $path;
            $this->singleAudioUploaded = true;
            return $path;
        }

        if ($this->singleAudioUploaded) {
            return $this->existingSingleAudio;
        }

        return $this->book ? $this->existingSingleAudio : null;
    }

    private function handleSingleVideoUpload(): ?string
    {
        if ($this->singleVideo) {
            if ($this->book && $this->existingSingleVideo && Storage::disk('public')->exists($this->existingSingleVideo)) {
                Storage::disk('public')->delete($this->existingSingleVideo);
            }

            $path = $this->singleVideo->store('book-videos', 'public');
            $this->existingSingleVideo = $path;
            $this->singleVideoUploaded = true;
            return $path;
        }

        if ($this->singleVideoUploaded) {
            return $this->existingSingleVideo;
        }

        return $this->book ? $this->existingSingleVideo : null;
    }

    private function handleChapterAudiosUpload(): ?array
    {
        return !empty($this->chapterAudios) ? array_values($this->chapterAudios) : ($this->book ? $this->existingChapterAudios : null);
    }

    private function handleChapterVideosUpload(): ?array
    {
        return !empty($this->chapterVideos) ? array_values($this->chapterVideos) : ($this->book ? $this->existingChapterVideos : null);
    }

    public function cancel()
    {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::ADMIN, UserRole::OWNER => redirect()->route('admin.book-management'),
            UserRole::AUTHOR => redirect()->route('author.books.index'),
            UserRole::TEACHER => redirect()->route('books.index'),
            default => redirect()->back(),
        };
    }

    public function removeExistingSamplePdfFile(): void
    {
        $this->removeSamplePdfFile = true;
        $this->existingSamplePdfFile = null;
    }

    public function removeExistingCoverImage(): void
    {
        if ($this->existingCoverImage && Storage::disk('public')->exists($this->existingCoverImage)) {
            Storage::disk('public')->delete($this->existingCoverImage);
        }
        $this->removeCoverImage = true;
        $this->existingCoverImage = null;
        $this->coverImageUploaded = false;
    }

    public function removeExistingPdfFile(): void
    {
        if ($this->existingPdfFile && Storage::disk('public')->exists($this->existingPdfFile)) {
            Storage::disk('public')->delete($this->existingPdfFile);
        }
        $this->removePdfFile = true;
        $this->existingPdfFile = null;
        $this->pdfFileUploaded = false;
    }

    public function removeExistingSingleAudioFile(): void
    {
        if ($this->existingSingleAudio && Storage::disk('public')->exists($this->existingSingleAudio)) {
            Storage::disk('public')->delete($this->existingSingleAudio);
        }
        $this->removeSingleAudioFile = true;
        $this->existingSingleAudio = null;
        $this->singleAudioUploaded = false;
    }

    public function removeExistingSingleVideoFile(): void
    {
        if ($this->existingSingleVideo && Storage::disk('public')->exists($this->existingSingleVideo)) {
            Storage::disk('public')->delete($this->existingSingleVideo);
        }
        $this->removeSingleVideoFile = true;
        $this->existingSingleVideo = null;
        $this->singleVideoUploaded = false;
    }

    public function addChapterAudio(): void
    {
        // Not needed anymore - using multiple file input
    }

    public function addChapterAudioD(): void
    {
        // Not needed - files stored immediately on upload
    }

    public function removeChapterAudio($index): void
    {
        if (isset($this->chapterAudios[$index])) {
            $item = $this->chapterAudios[$index];
            $filePath = is_array($item) ? ($item['file'] ?? null) : $item;

            if ($this->book && $filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            unset($this->chapterAudios[$index]);
            $this->chapterAudios = array_values($this->chapterAudios);
        }
    }

    public function addChapterVideo(): void
    {
        // Not needed anymore - using multiple file input
    }

    public function removeChapterVideo($index): void
    {
        if (isset($this->chapterVideos[$index])) {
            $item = $this->chapterVideos[$index];
            $filePath = is_array($item) ? ($item['file'] ?? null) : $item;

            if ($this->book && $filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            unset($this->chapterVideos[$index]);
            $this->chapterVideos = array_values($this->chapterVideos);
        }
    }

    public function getPageTitleProperty(): string
    {
        return $this->mode === 'edit' ? 'Edit Book' : 'Create New Book';
    }

    public function getSubmitButtonTextProperty(): string
    {
        return $this->mode === 'edit' ? 'Update Book' : 'Create Book';
    }

    public function getPublishingStatusOptionsProperty(): array
    {
        return PublishingStatus::getOptions();
    }

    public function getAgeGroupOptionsProperty(): array
    {
        return [
            '0-3' => '0-3 years',
            '4-6' => '4-6 years',
            '7-9' => '7-9 years',
            '10-12' => '10-12 years',
            '13-15' => '13-15 years',
            '16-18' => '16-18 years',
            '18+' => '18+ years',
        ];
    }

    public function getAvailableChaptersProperty(): array
    {
        return collect($this->tableOfContents)->map(function ($chapter) {
            return [
                'value' => $chapter['chapter'],
                'label' => "Chapter {$chapter['chapter']}: {$chapter['title']}",
            ];
        })->toArray();
    }

    public function getChapterMediaSummaryProperty(): array
    {
        $summary = [];

        foreach ($this->tableOfContents as $chapter) {
            $chapterNum = $chapter['chapter'];
            $videos = collect($this->chapterVideos)->where('chapter', $chapterNum)->count();
            $audios = collect($this->chapterAudios)->where('chapter', $chapterNum)->count();

            $summary[$chapterNum] = [
                'title' => $chapter['title'],
                'videos' => $videos,
                'audios' => $audios,
            ];
        }

        return $summary;
    }

    public function render()
    {
        return view('livewire.books.book-form');
    }
}
