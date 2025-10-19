<?php

namespace App\Livewire\UserBooks;

use App\Mail\BookShared;
use App\Models\UserBook;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserBookForm extends Component
{
    use WithFileUploads;

    public $mode = 'create';
    public $userBookId = null;
    public $userBook = null;

    // Basic book information
    public $title;
    public $description;
    public $edition;
    public $publisher;
    public $pages;
    public $annualSubscriptionFee = 0;
    public $subscriptionConditions;
    public $status = 'draft';

    // Files
    public $coverImage;
    public $pdfFile;
    public $samplePdfFile;
    public $singleAudio;
    public $singleVideo;
    public $chapterAudios = [];
    public $chapterVideos = [];

    // Existing files
    public $existingCoverImage = null;
    public $existingPdfFile = null;
    public $existingSamplePdfFile = null;
    public $existingSingleAudio = null;
    public $existingSingleVideo = null;
    public $existingChapterAudios = [];
    public $existingChapterVideos = [];

    // Removal flags
    public $removeCoverImage = false;
    public $removePdfFile = false;
    public $removeSamplePdfFile = false;
    public $removeSingleAudioFile = false;
    public $removeSingleVideoFile = false;
    public $removeChapterAudioFiles = [];
    public $removeChapterVideoFiles = [];

    // Table of Contents
    public $tableOfContents = [];
    public $showTableOfContents = false;
    public $expandedChapters = [];

    // Sharing
    public $emails = '';
    public $maxShares = 10; // Limit to 10 shares

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|string',
        'edition' => 'nullable|string|max:50',
        'publisher' => 'nullable|string|max:255',
        'pages' => 'nullable|integer|min:1|max:9999',
        'annualSubscriptionFee' => 'nullable|numeric|min:0|max:999999.99',
        'subscriptionConditions' => 'nullable|string',
        'coverImage' => 'nullable|image|max:2048',
        'pdfFile' => 'nullable|mimes:pdf|max:1024000',
        'samplePdfFile' => 'nullable|mimes:pdf|max:102400',
        'singleAudio' => 'nullable|file|mimes:mp3,wav,ogg|max:512000',
        'singleVideo' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:5242880',
        'chapterAudios.*' => 'nullable|file|mimes:mp3,wav,ogg|max:512000',
        'chapterVideos.*' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:1024000',
        'status' => 'required|in:draft,published,archived',
        'emails' => 'nullable|string',

        // Table of contents validation
        'tableOfContents.*.title' => 'required|string|max:255',
        'tableOfContents.*.chapter' => 'required|integer|min:1',
        'tableOfContents.*.description' => 'nullable|string',
        'tableOfContents.*.page_start' => 'required|integer|min:1',
        'tableOfContents.*.page_end' => 'required|integer|min:1',
        'tableOfContents.*.sections.*.title' => 'required|string|max:255',
        'tableOfContents.*.sections.*.page_start' => 'required|integer|min:1',
        'tableOfContents.*.sections.*.page_end' => 'required|integer|min:1',
        'tableOfContents.*.sections.*.description' => 'nullable|string',
    ];

    protected $messages = [
        'tableOfContents.*.title.required' => 'Chapter title is required.',
        'tableOfContents.*.chapter.required' => 'Chapter number is required.',
        'tableOfContents.*.page_start.required' => 'Start page is required.',
        'tableOfContents.*.page_end.required' => 'End page is required.',
        'tableOfContents.*.sections.*.title.required' => 'Section title is required.',
        'tableOfContents.*.sections.*.page_start.required' => 'Section start page is required.',
        'tableOfContents.*.sections.*.page_end.required' => 'Section end page is required.',
    ];

    public function mount(UserBook $userBook = null): void
    {

        if ($userBook && $userBook->exists) {
            $this->userBook = $userBook;
            $this->userBookId = $userBook->id;
            $this->mode = 'edit';
            $this->loadUserBookData();
        } else {
            $this->mode = 'create';
            $this->initializeTableOfContents();
        }
    }

public function loadUserBookData(): void
{
    $this->title = $this->userBook->title;
    $this->description = $this->userBook->description;
    $this->edition = $this->userBook->edition;
    $this->publisher = $this->userBook->publisher;
    $this->pages = $this->userBook->pages;
    $this->annualSubscriptionFee = $this->userBook->annual_subscription_fee;
    $this->subscriptionConditions = $this->userBook->subscription_conditions;
    $this->status = $this->userBook->status;

    // Files
    $this->existingCoverImage = $this->userBook->cover_image;
    $this->existingPdfFile = $this->userBook->content_url;
    $this->existingSamplePdfFile = $this->userBook->sample_url;
    $this->existingSingleAudio = $this->userBook->single_audio;
    $this->existingSingleVideo = $this->userBook->single_video;
    $this->existingChapterAudios = $this->userBook->chapter_audios ?? [];
    $this->existingChapterVideos = $this->userBook->chapter_videos ?? [];

    // Table of contents
    if ($this->userBook->table_of_contents) {
        $this->tableOfContents = $this->userBook->table_of_contents;
        $this->showTableOfContents = true;
    } else {
        $this->initializeTableOfContents();
    }

    // Pre-fill emails with existing shares
    $existingShares = $this->userBook->shares()->where('status', 'pending')->get();

    if ($existingShares->isNotEmpty()) {
        $this->emails = $existingShares->pluck('shared_to_email')->implode(', ');
    }
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

    public function updatedTitle(): void
    {
        if ($this->mode === 'create') {
            $this->generateSlug();
        }
    }

    public function generateSlug(): void
    {
        $this->slug = Str::slug($this->title);
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

    public function removeExistingSamplePdfFile(): void
    {
        $this->removeSamplePdfFile = true;
        $this->existingSamplePdfFile = null;
    }

    public function removeExistingSingleAudioFile(): void
    {
        $this->removeSingleAudioFile = true;
        $this->existingSingleAudio = null;
    }

    public function removeExistingSingleVideoFile(): void
    {
        $this->removeSingleVideoFile = true;
        $this->existingSingleVideo = null;
    }

    public function removeChapterAudioFile($chapterIndex): void
    {
        $this->removeChapterAudioFiles[$chapterIndex] = true;
        unset($this->existingChapterAudios[$chapterIndex]);
    }

    public function removeChapterVideoFile($chapterIndex): void
    {
        $this->removeChapterVideoFiles[$chapterIndex] = true;
        unset($this->existingChapterVideos[$chapterIndex]);
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

    private function handleFiles(): array
    {
        $fileData = [
            'cover_image' => $this->existingCoverImage,
            'content_url' => $this->existingPdfFile,
            'sample_url' => $this->existingSamplePdfFile,
            'single_audio' => $this->existingSingleAudio,
            'single_video' => $this->existingSingleVideo,
            'chapter_audios' => $this->existingChapterAudios ?? [],
            'chapter_videos' => $this->existingChapterVideos ?? [],
        ];

        // Handle cover image
        if ($this->removeCoverImage && $this->existingCoverImage) {
            Storage::disk('public')->delete($this->existingCoverImage);
            $fileData['cover_image'] = null;
        }
        if ($this->coverImage) {
            if ($this->existingCoverImage) {
                Storage::disk('public')->delete($this->existingCoverImage);
            }
            $fileName = $this->generateFileName('cover.' . $this->coverImage->extension());
            $fileData['cover_image'] = $this->coverImage->storeAs('user-books/covers', $fileName, 'public');
        }

        // Handle PDF file
        if ($this->removePdfFile && $this->existingPdfFile) {
            Storage::disk('public')->delete($this->existingPdfFile);
            $fileData['content_url'] = null;
        }
        if ($this->pdfFile) {
            if ($this->existingPdfFile) {
                Storage::disk('public')->delete($this->existingPdfFile);
            }
            $fileName = $this->generateFileName('full.pdf');
            $fileData['content_url'] = $this->pdfFile->storeAs('user-books/pdfs', $fileName, 'public');
        }

        // Handle sample PDF file
        if ($this->removeSamplePdfFile && $this->existingSamplePdfFile) {
            Storage::disk('public')->delete($this->existingSamplePdfFile);
            $fileData['sample_url'] = null;
        }
        if ($this->samplePdfFile) {
            if ($this->existingSamplePdfFile) {
                Storage::disk('public')->delete($this->existingSamplePdfFile);
            }
            $fileName = $this->generateFileName('sample.pdf');
            $fileData['sample_url'] = $this->samplePdfFile->storeAs('user-books/samples', $fileName, 'public');
        }

        // Handle single audio file
        if ($this->removeSingleAudioFile && $this->existingSingleAudio) {
            Storage::disk('public')->delete($this->existingSingleAudio);
            $fileData['single_audio'] = null;
        }
        if ($this->singleAudio) {
            if ($this->existingSingleAudio) {
                Storage::disk('public')->delete($this->existingSingleAudio);
            }
            $fileName = $this->generateFileName('audio.' . $this->singleAudio->extension());
            $fileData['single_audio'] = $this->singleAudio->storeAs('user-books/audio', $fileName, 'public');
        }

        // Handle single video file
        if ($this->removeSingleVideoFile && $this->existingSingleVideo) {
            Storage::disk('public')->delete($this->existingSingleVideo);
            $fileData['single_video'] = null;
        }
        if ($this->singleVideo) {
            if ($this->existingSingleVideo) {
                Storage::disk('public')->delete($this->existingSingleVideo);
            }
            $fileName = $this->generateFileName('video.' . $this->singleVideo->extension());
            $fileData['single_video'] = $this->singleVideo->storeAs('user-books/video', $fileName, 'public');
        }

        // Handle chapter audio files
        if ($this->chapterAudios) {
            foreach ($this->chapterAudios as $index => $file) {
                if ($file) {
                    // Remove existing file if needed
                    if (isset($this->existingChapterAudios[$index])) {
                        Storage::disk('public')->delete($this->existingChapterAudios[$index]);
                    }

                    $fileName = $this->generateFileName("chapter-{$index}-audio." . $file->extension());
                    $path = $file->storeAs('user-books/audio/chapters', $fileName, 'public');
                    $fileData['chapter_audios'][$index] = $path;
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

                    $fileName = $this->generateFileName("chapter-{$index}-video." . $file->extension());
                    $path = $file->storeAs('user-books/video/chapters', $fileName, 'public');
                    $fileData['chapter_videos'][$index] = $path;
                }
            }
        }

        return $fileData;
    }

    private function generateFileName(string $suffix): string
    {
        $title = $this->title ?? 'untitled';
        $slug = Str::slug(Str::limit($title, 50, ''));
        $fileName = "user-book-{$slug}-" . time();

        if ($suffix) {
            $fileName .= '_' . $suffix;
        }

        return $fileName;
    }

    public function submit()
    {
        $this->validate();

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

        // Validate emails if provided
        if (!empty($this->emails)) {
            $emailArray = array_filter(array_map('trim', explode(',', $this->emails)));
            if (count($emailArray) > $this->maxShares) {
                $this->addError('emails', "You can only share with up to {$this->maxShares} users.");
                return;
            }
        }

        try {
            $fileData = $this->handleFiles();
            $tocData = $this->showTableOfContents ? $this->tableOfContents : null;

            if ($this->mode === 'create') {
                $this->userBook = UserBook::create(array_merge([
                    'user_id' => auth()->id(),
                    'title' => $this->title,
                    'description' => $this->description,
                    'edition' => $this->edition,
                    'publisher' => $this->publisher,
                    'pages' => $this->pages,
                    'annual_subscription_fee' => $this->annualSubscriptionFee,
                    'subscription_conditions' => $this->subscriptionConditions,
                    'status' => $this->status,
                    'table_of_contents' => $tocData,
                ], $fileData));
            } else {
                $this->userBook->update(array_merge([
                    'title' => $this->title,
                    'description' => $this->description,
                    'edition' => $this->edition,
                    'publisher' => $this->publisher,
                    'pages' => $this->pages,
                    'annual_subscription_fee' => $this->annualSubscriptionFee,
                    'subscription_conditions' => $this->subscriptionConditions,
                    'status' => $this->status,
                    'table_of_contents' => $tocData,
                ], $fileData));
            }

            // Handle sharing
            if (!empty($this->emails)) {
                $this->handleSharing();
            }

            session()->flash('message', $this->mode === 'create' ? 'Book created successfully!' : 'Book updated successfully!');
            return redirect()->route('user-books.index');
        } catch (\Exception $e) {
            logError($e->getMessage());
            $this->addError('general', 'An error occurred. Please try again.');
        }
    }

    private function handleSharing(): void
    {
        $emailArray = array_filter(array_map('trim', explode(',', $this->emails)));

        foreach ($emailArray as $email) {
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // Check if user exists with this email
            $user = User::where('email', $email)->first();

            if ($user) {
                // Check if already shared with this user
                $existingShare = $this->userBook->shares()
                    ->where('shared_to_user_id', $user->id)
                    ->first();

                if (!$existingShare) {
                  $share =   $this->userBook->shares()->create([
                        'shared_by_user_id' => auth()->id(),
                        'shared_to_user_id' => $user->id,
                        'shared_to_email' => $email,
                        'status' => 'pending',
                    ]);

                    // Send email notification
                    Mail::to($user->email)->send(new BookShared($share));

                }
            }
        }
    }

    public function cancel()
    {
        return redirect()->route('user-books.index');
    }

    public function render()
    {
        return view('livewire.user-books.user-book-form');
    }
}

