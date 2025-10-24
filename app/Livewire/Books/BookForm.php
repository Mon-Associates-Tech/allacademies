<?php

namespace App\Livewire\Books;

use App\Enums\PublishingStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Log;
use setasign\Fpdi\Fpdi;
use ValueError;
use App\Enums\UserRole;

class BookForm extends Component
{
    use WithFileUploads;

    // Component mode
    public $mode = 'create'; // 'create' or 'edit'
    public $bookId = null;
    public $book = null;

    // Basic book information
    public $title;
    public $slug;
    public $authorId;
    public $newAuthorName = '';
    public $newAuthorEmail = '';
    public $showNewAuthorForm = false;
    public $bookCategoryIds = [];
    public $newCategoryName = '';
    public $newCategoryDescription = '';
    public $showNewCategoryForm = false;
    public $edition;
    public $publisher;
    public $pages;
    public $hasHardcopy = false;
    public $hasSoftcopy = false;
    public $additionalInfo;
    public $coverImage;
    #[Validate('nullable|file|mimes:pdf|max:102400')]
    public $pdfFile;
    public $annualSubscriptionFee = 0;
    public $subscriptionConditions;
    public $status = 'published';

    // Existing files
    public $existingCoverImage = null;
    public $existingPdfFile = null;
    public $removeCoverImage = false;
    public $removePdfFile = false;

    // Table of Contents
    public $tableOfContents = [];
    public $showTableOfContents = false;
    public $expandedChapters = [];

    // Data collections
    public $authors;
    public $bookCategories;

    public $hasAudio = false;
    public $hasVideo = false;
    public $singleAudioUpload = false;
    public $singleVideoUpload = false;
    public $singleAudio;
    #[Validate('nullable|file|mimes:mp4,mov,avi,mkv,webm|max:524288')]
    public $singleVideo;
    public $chapterAudios = [];
    public $chapterVideos = [];
    public $existingSingleAudio = null;
    public $existingSingleVideo = null;
    public $existingChapterAudios = [];
    public $existingChapterVideos = [];
    public $removeSingleAudioFile = false;
    public $removeSingleVideoFile = false;
    public $removeChapterAudioFiles = [];
    public $removeChapterVideoFiles = [];

    public $uploadProgress = [];
    public $uploadComplete = [];


    #[Validate('nullable|file|mimes:pdf|max:10240')] // 10MB limit for sample
    public $samplePdfFile;

    public $existingSamplePdfFile = null;
    public $removeSamplePdfFile = false;

    protected $listeners = [
        'update-authorId' => 'updateAuthorId',
        'update-bookCategoryIds' => 'updateBookCategoryIds',
    ];

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'authorId' => 'required|exists:authors,id',
        'bookCategoryIds' => 'required|exists:book_categories,id',
        'edition' => 'nullable|string|max:50',
        'publisher' => 'nullable|string|max:255',
        'pages' => 'nullable|integer|min:1|max:9999',
        'hasHardcopy' => 'boolean',
        'hasSoftcopy' => 'boolean',
        'additionalInfo' => 'nullable|string',
        'annualSubscriptionFee' => 'nullable|numeric|min:0|max:999999.99',
        'subscriptionConditions' => 'nullable|string',
        'coverImage' => 'nullable|image|max:2048',
        'samplePdfFile' => 'nullable|mimes:pdf|max:10240',
        'pdfFile' => 'nullable|mimes:pdf|max:10240',
        'status' => 'required|in:draft,published',
        'newAuthorName' => 'required_if:showNewAuthorForm,true|string|max:255',
        'newAuthorEmail' => 'required_if:showNewAuthorForm,true|email|unique:users,email',
        'newCategoryName' => 'required_if:showNewCategoryForm,true|string|max:255',
        'newCategoryDescription' => 'nullable|string',

        'tableOfContents.*.title' => 'required|string|max:255',
        'tableOfContents.*.chapter' => 'required|integer|min:1',
        'tableOfContents.*.description' => 'nullable|string',
        'tableOfContents.*.page_start' => 'required|integer|min:1',
        'tableOfContents.*.page_end' => 'required|integer|min:1',

        'tableOfContents.*.sections.*.title' => 'required|string|max:255',
        'tableOfContents.*.sections.*.page_start' => 'required|integer|min:1',
        'tableOfContents.*.sections.*.page_end' => 'required|integer|min:1',
        'tableOfContents.*.sections.*.description' => 'nullable|string',

        'hasAudio' => 'boolean',
        'hasVideo' => 'boolean',
        'singleAudio' => 'nullable|file|mimes:mp3,wav,ogg|max:51200', // 50MB max
        'singleVideo' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:524288',
        'chapterAudios.*' => 'nullable|file|mimes:mp3,wav,ogg|max:51200',
        'chapterVideos.*' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:102400',

    ];

    protected $messages = [
        'newAuthorName.required_if' => 'Author name is required when adding a new author.',
        'newAuthorEmail.required_if' => 'Author email is required when adding a new author.',
        'newAuthorEmail.unique' => 'This email is already registered.',
        'newCategoryName.required_if' => 'Category name is required when adding a new category.',
        'tableOfContents.*.title.required' => 'Chapter title is required.',
        'tableOfContents.*.chapter.required' => 'Chapter number is required.',
        'tableOfContents.*.page_start.required' => 'Start page is required.',
        'tableOfContents.*.page_end.required' => 'End page is required.',

        'tableOfContents.*.sections.*.title.required' => 'Section title is required.',
        'tableOfContents.*.sections.*.page_start.required' => 'Section start page is required.',
        'tableOfContents.*.sections.*.page_end.required' => 'Section end page is required.',
    ];

    public function mount(Book $book = null): void
    {
        if ($book && $book->exists) {
            $this->book = $book;
            $this->bookId = $book->id;
            $this->mode = 'edit';
        } else {
            $this->mode = 'create';
            $this->book = null;
            $this->bookId = null;
        }

        $this->loadData();

        if ($this->mode === 'edit') {
            $this->loadBookData();
            $this->authorizeBookAccess();
        } else {
            $this->initializeTableOfContents();
            $this->status = PublishingStatus::default()->value;
        }
    }

    public function loadData(): void
    {
        $this->authors = Author::with('user')->orderBy('id')->get();
        $this->bookCategories = BookCategory::orderBy('name')->get();
    }

    public function loadBookData(): void
    {
        $this->book = Book::with(['author.user', 'bookCategory', 'categories'])->findOrFail($this->bookId);

        // Populate form fields
        $this->title = $this->book->title;
        $this->slug = $this->book->slug;
        $this->authorId = $this->book->author_id;
//        $this->bookCategoryId = $this->book->book_category_id;
        $this->existingSamplePdfFile = $this->book->getAttributes()['sample_url'] ?? null;
        $this->bookCategoryIds = $this->book->categories->pluck('id')->toArray();
        $this->edition = $this->book->edition;
        $this->publisher = $this->book->publisher;
        $this->pages = $this->book->pages;
        $this->hasHardcopy = $this->book->has_hardcopy;
        $this->hasSoftcopy = $this->book->has_softcopy;
        $this->additionalInfo = $this->book->additional_info;
        $this->annualSubscriptionFee = $this->book->annual_subscription_fee ?? 0;
        $this->subscriptionConditions = $this->book->subscription_conditions;
        $this->status = $this->book->status;

        // Handle existing files - use the raw database field, not the accessor
        $this->existingCoverImage = $this->book->getAttributes()['cover_image'] ?? null;
        $this->existingPdfFile = $this->book->getAttributes()['content_url'] ?? null;

        // Handle table of contents
        if ($this->book->table_of_contents) {
            $this->tableOfContents = $this->book->table_of_contents;
            $this->showTableOfContents = true;
        } else {
            $this->initializeTableOfContents();
        }

        $this->hasAudio = $this->book->has_audio;
        $this->hasVideo = $this->book->has_video;
        $this->existingSingleAudio = $this->book->getAttributes()['single_audio'] ?? null;
        $this->existingSingleVideo = $this->book->getAttributes()['single_video'] ?? null;
        $this->existingChapterAudios = $this->book->chapter_audios ?? [];
        $this->existingChapterVideos = $this->book->chapter_videos ?? [];

    }

    public function initializeTableOfContents(): void
    {
        if (empty($this->tableOfContents)) {
            $this->tableOfContents = [
                [
                    'chapter' => 1,
                    'title' => 'Introduction',
                    'description' => '',
                    'page_start' => 1,
                    'page_end' => 10,
                    'sections' => []
                ]
            ];
        }
    }

    private function authorizeBookAccess(): void
    {
        $user = auth()->user();

        // Admin and owner can edit any book
        if (in_array($user->role, [UserRole::ADMIN, UserRole::OWNER])) {
            return;
        }

        // Authors can only edit their own books
        if ($user->role === UserRole::AUTHOR && $this->book->author->user_id === $user->id) {
            return;
        }

        // Teachers can edit books if they have permission (you might want to add a specific permission check)
        if ($user->role === UserRole::TEACHER) {
            // Add your teacher permission logic here
            // For now, allowing all teachers to edit books
            return;
        }

        // If none of the above conditions are met, deny access
        abort(403, 'You are not authorized to edit this book.');
    }

    public function updatedSingleVideo(): void
    {
        $this->uploadComplete['singleVideo'] = true;
        $this->validateOnly('singleVideo');
    }

    public function updatedChapterVideos($value, $key): void
    {
        $this->uploadComplete["chapterVideos.{$key}"] = true;
        $this->validateOnly("chapterVideos.{$key}");
    }

    public function updateAuthorId($value): void
    {
        $this->authorId = $value;
    }

    public function updateBookCategoryIds($value): void
    {
        $this->bookCategoryIds = is_array($value) ? $value : [$value];
    }

    public function cancel()
    {
        $user = auth()->user();

        return match ($user->role) {
            'admin', 'owner' => redirect()->route('admin.book-management'),
            'author' => redirect()->route('author.books.index'),
            'teacher' => redirect()->route('books.index'),
            default => redirect()->back(),
        };
    }

    public function removeExistingSamplePdfFile(): void
    {
        $this->removeSamplePdfFile = true;
        $this->existingSamplePdfFile = null;
    }

    public function getPublishingStatusOptionsProperty()
    {
        return PublishingStatus::getOptions();
    }


    // Get publishing status options for the view

    public function getCurrentPublishingStatusProperty()
    {
        try {
            return PublishingStatus::from($this->status);
        } catch (ValueError $e) {
            return PublishingStatus::default();
        }
    }

    // Get current publishing status enum

    public function getPageTitleProperty()
    {
        return $this->mode === 'edit' ? 'Edit Book' : 'Create New Book';
    }

    public function getSubmitButtonTextProperty()
    {
        return $this->mode === 'edit' ? 'Update Book' : 'Create Book';
    }

    public function updatedTitle(): void
    {
        if ($this->mode === 'create') {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedPages(): void
    {
        if ($this->pages && !$this->showTableOfContents) {
            $this->generateTableOfContents();
        }
    }

    public function generateTableOfContents(): void
    {
        if (!$this->pages) return;

        $chaptersCount = max(1, min(15, intval($this->pages / 20)));
        $this->tableOfContents = [];

        for ($i = 1; $i <= $chaptersCount; $i++) {
            $this->tableOfContents[] = [
                'chapter' => $i,
                'title' => "Chapter {$i}",
                'description' => "Content for chapter {$i}",
                'page_start' => (($i - 1) * intval($this->pages / $chaptersCount)) + 1,
                'page_end' => $i * intval($this->pages / $chaptersCount),
                'sections' => []
            ];
        }
    }

    public function toggleNewAuthorForm(): void
    {
        $this->showNewAuthorForm = !$this->showNewAuthorForm;
        $this->reset(['newAuthorName', 'newAuthorEmail']);
        $this->resetValidation(['newAuthorName', 'newAuthorEmail']);
    }

    public function toggleNewCategoryForm(): void
    {
        $this->showNewCategoryForm = !$this->showNewCategoryForm;
        $this->reset(['newCategoryName', 'newCategoryDescription']);
        $this->resetValidation(['newCategoryName', 'newCategoryDescription']);
    }

    public function createNewAuthor(): void
    {
        $this->validate([
            'newAuthorName' => 'required|string|max:255',
            'newAuthorEmail' => 'required|email|unique:users,email',
        ]);

        DB::beginTransaction();
        try {
            // todo don't always create a user for an author
            // Create user for author
            $user = User::create([
                'name' => $this->newAuthorName,
                'email' => $this->newAuthorEmail,
                'password' => bcrypt('defaultpassword123'),
                'role' => 'author',
                'email_verified_at' => now(),
            ]);

            // Create author
            $author = Author::create([
                'user_id' => $user->id,
            ]);

            // Refresh authors list
            $this->loadData();

            // Select the new author
            $this->authorId = $author->id;

            // Hide the form
            $this->showNewAuthorForm = false;
            $this->reset(['newAuthorName', 'newAuthorEmail']);

            DB::commit();
            session()->flash('message', 'New author created successfully!');
        } catch (Exception $e) {
            logError('Exception in createNewAuthor:' . $e);
            DB::rollback();
            $this->addError('newAuthorEmail', 'Failed to create author. Please try again.');
        }
    }

    public function createNewCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:book_categories,name',
            'newCategoryDescription' => 'nullable|string',
        ]);

        try {
            // Create new category
            $category = BookCategory::create([
                'name' => $this->newCategoryName,
                'description' => $this->newCategoryDescription,
            ]);

            // Refresh categories list
            $this->loadData();

            // Select the new category
            $this->bookCategoryIds[] = $category->id;

            // Hide the form and reset fields
            $this->showNewCategoryForm = false;
            $this->reset(['newCategoryName', 'newCategoryDescription']);

            session()->flash('message', 'New category created successfully!');
        } catch (Exception $e) {
            logError('Exception in createNewCategory:' . $e);
            $this->addError('newCategoryName', 'Failed to create category. Please try again.');
        }
    }

    public function toggleTableOfContents(): void
    {
        $this->showTableOfContents = !$this->showTableOfContents;
        if ($this->showTableOfContents && empty($this->tableOfContents)) {
            $this->generateTableOfContents();
        }
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
            'sections' => []
        ];
    }

    public function removeChapter($index): void
    {
        if (count($this->tableOfContents) > 1) {
            unset($this->tableOfContents[$index]);
            $this->tableOfContents = array_values($this->tableOfContents);
        }
    }

    public function addSection($chapterIndex): void
    {
        $chapter = $this->tableOfContents[$chapterIndex];
        $lastSection = end($chapter['sections']);

        // Calculate section page range within chapter bounds
        $sectionPageStart = $lastSection ? $lastSection['page_end'] + 1 : $chapter['page_start'];
        $sectionPageEnd = min($sectionPageStart + 2, $chapter['page_end']);

        $this->tableOfContents[$chapterIndex]['sections'][] = [
            'title' => 'New Section',
            'page_start' => $sectionPageStart,
            'page_end' => $sectionPageEnd,
            'description' => ''
        ];

        // Auto-expand the chapter when adding a section
        if (!in_array($chapterIndex, $this->expandedChapters)) {
            $this->expandedChapters[] = $chapterIndex;
        }
    }

    public function removeSection($chapterIndex, $sectionIndex): void
    {
        unset($this->tableOfContents[$chapterIndex]['sections'][$sectionIndex]);
        $this->tableOfContents[$chapterIndex]['sections'] = array_values($this->tableOfContents[$chapterIndex]['sections']);
    }

    public function generateSections($chapterIndex): void
    {
        $chapter = $this->tableOfContents[$chapterIndex];
        $chapterPageRange = $chapter['page_end'] - $chapter['page_start'] + 1;

        if ($chapterPageRange < 3) {
            return; // Too few pages for multiple sections
        }

        $sectionsCount = min(4, max(2, intval($chapterPageRange / 3))); // 2-4 sections per chapter
        $pagesPerSection = intval($chapterPageRange / $sectionsCount);

        $this->tableOfContents[$chapterIndex]['sections'] = [];

        for ($i = 1; $i <= $sectionsCount; $i++) {
            $sectionPageStart = $chapter['page_start'] + (($i - 1) * $pagesPerSection);
            $sectionPageEnd = ($i === $sectionsCount) ?
                $chapter['page_end'] :
                $sectionPageStart + $pagesPerSection - 1;

            $this->tableOfContents[$chapterIndex]['sections'][] = [
                'title' => "Section {$i}",
                'page_start' => $sectionPageStart,
                'page_end' => $sectionPageEnd,
                'description' => "Content for section {$i}"
            ];
        }

        // Auto-expand the chapter
        if (!in_array($chapterIndex, $this->expandedChapters)) {
            $this->expandedChapters[] = $chapterIndex;
        }
    }

    public function removeExistingCoverImage(): void
    {
        $this->removeCoverImage = true;
        $this->existingCoverImage = null;
    }

    public function removeExistingPdfFile(): void
    {
        $this->removePdfFile = true;
        $this->existingPdfFile = null;
    }

    public function submit()
    {
        $this->validate();

        // Validate that at least one format is selected
        if (!$this->hasHardcopy && !$this->hasSoftcopy) {
            $this->addError('hasHardcopy', 'Please select at least one format (hardcopy or softcopy).');
            return;
        }

        // If softcopy is selected but no PDF file provided and no existing PDF
        if ($this->hasSoftcopy && !$this->pdfFile && (!$this->existingPdfFile || $this->removePdfFile)) {
            $this->addError('pdfFile', 'PDF file is required for softcopy books.');
            return;
        }

        // Additional validation for table of contents
        if ($this->showTableOfContents && !empty($this->tableOfContents)) {
            $errors = $this->validateTableOfContents();
            if (!empty($errors)) {
                foreach ($errors as $field => $message) {
                    $this->addError($field, $message);
                }
                return;
            }
        }

        DB::beginTransaction();
        try {
            if ($this->mode === 'create') {
                $this->createBook();
            } else {
                $this->updateBook();
            }

            DB::commit();

            $statusLabel = PublishingStatus::from($this->status)->getLabel();
            $action = $this->mode === 'create' ? 'created' : 'updated';
            session()->flash('message', "Book {$action} successfully and saved as {$statusLabel}!");
            $user = auth()->user();

            return match ($user->role) {
                'admin', 'owner' => redirect()->route('admin.book-management'),
                'author' => redirect()->route('author.books.index'),
                'teacher' => redirect()->route('books.index'),
                default => redirect()->back(),
            };

        } catch (Exception $e) {
            logError("Exception in {$this->mode}: " . $e);
            DB::rollback();
            $this->addError('general', "Failed to {$this->mode} book. Please try again.");
        }
    }

    private function validateTableOfContents(): array
    {
        $errors = [];

        foreach ($this->tableOfContents as $chapterIndex => $chapter) {
            // Validate chapter page ranges
            if ($chapter['page_start'] >= $chapter['page_end']) {
                $errors["tableOfContents.{$chapterIndex}.page_end"] = 'Chapter end page must be greater than start page.';
            }

            // Validate sections
            if (!empty($chapter['sections'])) {
                foreach ($chapter['sections'] as $sectionIndex => $section) {
                    // Section pages must be within chapter bounds
                    if ($section['page_start'] < $chapter['page_start'] || $section['page_end'] > $chapter['page_end']) {
                        $errors["tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_range"] = 'Section pages must be within chapter page range.';
                    }

                    // Section page range validation
                    if ($section['page_start'] >= $section['page_end']) {
                        $errors["tableOfContents.{$chapterIndex}.sections.{$sectionIndex}.page_end"] = 'Section end page must be greater than start page.';
                    }
                }
            }
        }

        return $errors;
    }

    private function createBook(): void
    {
        // Handle cover image
        $coverPath = null;
        if ($this->coverImage) {
            $fileName = $this->generateFileName(null, 'cover.' . $this->coverImage->extension());
            $coverPath = $this->coverImage->storeAs('book-covers', $fileName, 'public');
        }

        // Handle PDF file
        $pdfPath = null;
        if ($this->pdfFile) {
            $fileName = $this->generateFileName(null, 'full.pdf');
            $pdfPath = $this->pdfFile->storeAs('book-pdfs', $fileName, 'public');
        }

        // Prepare table of contents
        $tocData = $this->showTableOfContents ? $this->tableOfContents : null;

        $mediaData = $this->handleMediaFiles();

        // Create book
        $book = Book::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'author_id' => $this->authorId,
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'has_hardcopy' => $this->hasHardcopy,
            'has_softcopy' => $this->hasSoftcopy,
            'additional_info' => $this->additionalInfo,
            'annual_subscription_fee' => $this->annualSubscriptionFee,
            'subscription_conditions' => $this->subscriptionConditions,
            'cover_image' => $coverPath,
            'sample_url' => '',
            'content_url' => $pdfPath,
            'table_of_contents' => $tocData,
            'status' => $this->status,
            'has_audio' => $mediaData['has_audio'],
            'has_video' => $mediaData['has_video'],
        ]);

        $book->categories()->attach($this->bookCategoryIds);
        $this->handleSamplePdfFile($book);

        if ($book->has_audio || $book->has_video) {
            $book->media()->create([
                'single_audio' => $mediaData['single_audio'],
                'single_video' => $mediaData['single_video'],
                'chapter_audios' => $mediaData['chapter_audios'],
                'chapter_videos' => $mediaData['chapter_videos'],
            ]);
        }
    }

    private function handleMediaFiles(): array
    {
        $mediaData = [
            'has_audio' => $this->hasAudio,
            'has_video' => $this->hasVideo,
            'single_audio' => $this->existingSingleAudio,
            'single_video' => $this->existingSingleVideo,
            'chapter_audios' => $this->existingChapterAudios ?? [],
            'chapter_videos' => $this->existingChapterVideos ?? [],
        ];

        // Handle single audio file
        if ($this->removeSingleAudioFile && $this->existingSingleAudio) {
            Storage::disk('public')->delete($this->existingSingleAudio);
            $mediaData['single_audio'] = null;
        }
        if ($this->singleAudio) {
            if ($this->existingSingleAudio) {
                Storage::disk('public')->delete($this->existingSingleAudio);
            }
            $fileName = $this->generateFileName($this->book ?? null, 'audio.' . $this->singleAudio->extension());
            $mediaData['single_audio'] = $this->singleAudio->storeAs('book-audio', $fileName, 'public');
        }

        // Handle single video file
        if ($this->removeSingleVideoFile && $this->existingSingleVideo) {
            Storage::disk('public')->delete($this->existingSingleVideo);
            $mediaData['single_video'] = null;
        }
        if ($this->singleVideo) {
            if ($this->existingSingleVideo) {
                Storage::disk('public')->delete($this->existingSingleVideo);
            }
            $fileName = $this->generateFileName($this->book ?? null, 'video.' . $this->singleVideo->extension());
            $mediaData['single_video'] = $this->singleVideo->storeAs('book-video', $fileName, 'public');
        }

        // Handle chapter audio files
        if ($this->chapterAudios) {
            foreach ($this->chapterAudios as $index => $file) {
                if ($file) {
                    // Remove existing file if needed
                    if (isset($this->existingChapterAudios[$index])) {
                        Storage::disk('public')->delete($this->existingChapterAudios[$index]);
                    }

                    $fileName = $this->generateFileName($this->book ?? null, "chapter-{$index}-audio." . $file->extension());
                    $path = $file->storeAs('book-audio/chapters', $fileName, 'public');
                    $mediaData['chapter_audios'][$index] = $path;
                }
            }
        }

        // Handle chapter video files
        if ($this->chapterVideos) {
            foreach ($this->chapterVideos as $index => $file) {
                if ($file) {
                    // Remove existing file if needed
                    if (isset($this->existingChapterVideos[$index])) {
                        Storage::disk('public')->delete($this->existingChapterVideos[$index]);
                    }

                    $fileName = $this->generateFileName($this->book ?? null, "chapter-{$index}-video." . $file->extension());
                    $path = $file->storeAs('book-video/chapters', $fileName, 'public');
                    $mediaData['chapter_videos'][$index] = $path;
                }
            }
        }

        return $mediaData;
    }

    private function handleSamplePdfFile($book): void
    {
        $samplePath = $book->getAttributes()['sample_url'] ?? null;

        if ($this->removeSamplePdfFile && $samplePath) {
            Storage::disk('public')->delete($samplePath);
            $samplePath = null;
        }

        if ($this->samplePdfFile) {
            if ($samplePath) {
                Storage::disk('public')->delete($samplePath);
            }

            // Create descriptive filename
            $fileName = $this->generateFileName($book, 'sample.pdf');
            $samplePath = $this->samplePdfFile->storeAs('book-samples', $fileName, 'public');
        } else if (!$samplePath && $this->pdfFile && $this->showTableOfContents && !empty($this->tableOfContents)) {
            // If no sample provided, try to extract a chapter from the full PDF
            $samplePath = $this->extractChapterAsSample($book);
        }

        if ($samplePath !== ($book->getAttributes()['sample_url'] ?? null)) {
            $book->update(['sample_url' => $samplePath]);
        }
    }

    /**
     * Generate a descriptive filename with book ID and slug
     *
     * @param Book|null $book
     * @param string $suffix
     * @return string
     */
    private function generateFileName(?Book $book, string $suffix): string
    {
        // Use existing book ID or temporary ID for new books
        $bookId = $book?->id ?? 'new';

        // Use current title or existing book title
        $title = $this->title ?? $book?->title ?? 'untitled';

        // Create slug from title
        $slug = Str::slug(Str::limit($title, 50, ''));

        // Generate filename with ID and slug
        $fileName = "book-{$bookId}_{$slug}";

        // Add suffix (file extension)
        if ($suffix) {
            $fileName .= '_' . $suffix;
        }

        return $fileName;
    }

    private function extractChapterAsSample(Book $book): ?string
    {
        // Check if we have a full PDF file and table of contents
        $contentUrl = $book->getAttributes()['content_url'] ?? null;
        if (!$contentUrl || empty($this->tableOfContents)) {
            return null;
        }

        try {
            // Get the path to the full PDF
            $fullPdfPath = Storage::disk('public')->path($contentUrl);

            // Check if the file exists
            if (!file_exists($fullPdfPath)) {
                return null;
            }

            // Create a new FPDI instance
            $pdf = new Fpdi();

            // Get the first chapter pages
            $firstChapter = $this->tableOfContents[0] ?? null;
            if (!$firstChapter) {
                return null;
            }

            $startPage = $firstChapter['page_start'] ?? 1;
            $endPage = $firstChapter['page_end'] ?? min(5, $book->pages ?? 5); // Limit to 5 pages if not specified

            // Import pages from the source PDF
            $pageCount = $pdf->setSourceFile($fullPdfPath);

            // Make sure page numbers are within bounds
            $startPage = max(1, min($startPage, $pageCount));
            $endPage = max($startPage, min($endPage, $pageCount));

            // Add pages to the new PDF
            for ($pageNo = $startPage; $pageNo <= $endPage; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            // Generate a descriptive filename for the sample
            $filename = $this->generateFileName($book, 'sample.pdf');
            $samplePath = 'book-samples/' . $filename;

            // Save the sample PDF
            $pdfContent = $pdf->Output('', 'S');
            Storage::disk('public')->put($samplePath, $pdfContent);

            return $samplePath;
        } catch (Exception $e) {
            // Log the error but don't break the flow
            Log::error('Error extracting sample PDF: ' . $e->getMessage());
            return null;
        }
    }

    private function updateBook(): void
    {
        // Handle cover image update - use the raw database field
        $coverPath = $this->book->getAttributes()['cover_image'];
        if ($this->removeCoverImage && $coverPath) {
            Storage::disk('public')->delete($coverPath);
            $coverPath = null;
        }
        if ($this->coverImage) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }
            $fileName = $this->generateFileName($this->book, 'cover.' . $this->coverImage->extension());
            $coverPath = $this->coverImage->storeAs('book-covers', $fileName, 'public');
        }

        // Handle PDF file update - use the raw database field
        $pdfPath = $this->book->getAttributes()['content_url'];
        if ($this->removePdfFile && $pdfPath) {
            Storage::disk('public')->delete($pdfPath);
            $pdfPath = null;
        }
        if ($this->pdfFile) {
            if ($pdfPath) {
                Storage::disk('public')->delete($pdfPath);
            }
            $fileName = $this->generateFileName($this->book, 'full.pdf');
            $pdfPath = $this->pdfFile->storeAs('book-pdfs', $fileName, 'public');
        }

        // Prepare table of contents
        $tocData = $this->showTableOfContents ? $this->tableOfContents : null;

        $mediaData = $this->handleMediaFiles();

        // Update book
        $this->book->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'author_id' => $this->authorId,
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'has_hardcopy' => $this->hasHardcopy,
            'has_softcopy' => $this->hasSoftcopy,
            'additional_info' => $this->additionalInfo,
            'annual_subscription_fee' => $this->annualSubscriptionFee,
            'subscription_conditions' => $this->subscriptionConditions,
            'cover_image' => $coverPath,
            'content_url' => $pdfPath,
            'table_of_contents' => $tocData,
            'status' => $this->status,
            'has_audio' => $mediaData['has_audio'],
            'has_video' => $mediaData['has_video'],
        ]);

        $this->handleSamplePdfFile($this->book);

        $this->book->categories()->sync($this->bookCategoryIds);

        $this->book->media()->update([
            'single_audio' => $mediaData['single_audio'],
            'single_video' => $mediaData['single_video'],
            'chapter_audios' => $mediaData['chapter_audios'],
            'chapter_videos' => $mediaData['chapter_videos'],
        ]);
    }

    public function removeExistingSingleAudioFile()
    {
        $this->removeSingleAudioFile = true;
        $this->existingSingleAudio = null;
    }

    public function removeExistingSingleVideoFile()
    {
        $this->removeSingleVideoFile = true;
        $this->existingSingleVideo = null;
    }

    public function removeChapterAudioFile($chapterIndex)
    {
        $this->removeChapterAudioFiles[$chapterIndex] = true;
        unset($this->existingChapterAudios[$chapterIndex]);
    }

    public function removeChapterVideoFile($chapterIndex)
    {
        $this->removeChapterVideoFiles[$chapterIndex] = true;
        unset($this->existingChapterVideos[$chapterIndex]);
    }

    public function redirectIntended($default = '/', $navigate = false)
    {

        return redirect()->intended($default)->with('success', 'Book updated successfully.');
    }

    public function render()
    {
        return view('livewire.books.book-form');
    }
}
