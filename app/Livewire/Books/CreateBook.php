<?php

namespace App\Livewire\Books;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Book;
use App\Models\Author;
use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateBook extends Component
{
    use WithFileUploads;

    // Basic book information
    public $title;
    public $slug;
    public $authorId;
    public $newAuthorName = '';
    public $newAuthorEmail = '';
    public $showNewAuthorForm = false;
    public $bookCategoryId;
    public $edition;
    public $publisher;
    public $pages;
    public $hasHardcopy = false;
    public $hasSoftcopy = false;
    public $additionalInfo;
    public $coverImage;
    public $pdfFile;
    public $annualSubscriptionFee = 0;
    public $subscriptionConditions;
    public $status = 'published';

    // Table of Contents
    public $tableOfContents = [];
    public $showTableOfContents = false;
    public $expandedChapters = [];


    // Data collections
    public $authors;
    public $bookCategories;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'authorId' => 'required|exists:authors,id',
        'bookCategoryId' => 'required|exists:book_categories,id',
        'edition' => 'nullable|string|max:50',
        'publisher' => 'nullable|string|max:255',
        'pages' => 'nullable|integer|min:1|max:9999',
        'hasHardcopy' => 'boolean',
        'hasSoftcopy' => 'boolean',
        'additionalInfo' => 'nullable|string',
        'annualSubscriptionFee' => 'nullable|numeric|min:0|max:999999.99',
        'subscriptionConditions' => 'nullable|string',
        'coverImage' => 'nullable|image|max:2048',
        'pdfFile' => 'nullable|mimes:pdf|max:10240',
        'status' => 'required|in:draft,published',
        'newAuthorName' => 'required_if:showNewAuthorForm,true|string|max:255',
        'newAuthorEmail' => 'required_if:showNewAuthorForm,true|email|unique:users,email',

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
        'newAuthorName.required_if' => 'Author name is required when adding a new author.',
        'newAuthorEmail.required_if' => 'Author email is required when adding a new author.',
        'newAuthorEmail.unique' => 'This email is already registered.',
        'tableOfContents.*.title.required' => 'Chapter title is required.',
        'tableOfContents.*.chapter.required' => 'Chapter number is required.',
        'tableOfContents.*.page_start.required' => 'Start page is required.',
        'tableOfContents.*.page_end.required' => 'End page is required.',

        'tableOfContents.*.sections.*.title.required' => 'Section title is required.',
        'tableOfContents.*.sections.*.page_start.required' => 'Section start page is required.',
        'tableOfContents.*.sections.*.page_end.required' => 'Section end page is required.',
    ];



    public function mount()
    {
        $this->loadData();
        $this->initializeTableOfContents();
    }

    public function loadData()
    {
        $this->authors = Author::with('user')->orderBy('id')->get();
        $this->bookCategories = BookCategory::orderBy('name')->get();
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function updatedPages()
    {
        if ($this->pages && !$this->showTableOfContents) {
            $this->generateTableOfContents();
        }
    }

    public function toggleNewAuthorForm()
    {
        $this->showNewAuthorForm = !$this->showNewAuthorForm;
        $this->reset(['newAuthorName', 'newAuthorEmail']);
        $this->resetValidation(['newAuthorName', 'newAuthorEmail']);
    }

    public function createNewAuthor()
    {
        $this->validate([
            'newAuthorName' => 'required|string|max:255',
            'newAuthorEmail' => 'required|email|unique:users,email',
        ]);

        DB::beginTransaction();
        try {
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
        } catch (\Exception $e) {
            logError('Exception in createNewAuthor:'. $e);
            DB::rollback();
            $this->addError('newAuthorEmail', 'Failed to create author. Please try again.');
        }
    }

    public function toggleTableOfContents()
    {
        $this->showTableOfContents = !$this->showTableOfContents;
        if ($this->showTableOfContents && empty($this->tableOfContents)) {
            $this->generateTableOfContents();
        }
    }

    public function toggleChapter($index)
    {
        if (in_array($index, $this->expandedChapters)) {
            $this->expandedChapters = array_diff($this->expandedChapters, [$index]);
        } else {
            $this->expandedChapters[] = $index;
        }
    }


    public function initializeTableOfContents()
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

    public function generateTableOfContents()
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

    public function addChapter()
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

    public function removeChapter($index)
    {
        if (count($this->tableOfContents) > 1) {
            unset($this->tableOfContents[$index]);
            $this->tableOfContents = array_values($this->tableOfContents);
        }
    }


    public function addSection($chapterIndex)
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

    public function removeSection($chapterIndex, $sectionIndex)
    {
        unset($this->tableOfContents[$chapterIndex]['sections'][$sectionIndex]);
        $this->tableOfContents[$chapterIndex]['sections'] = array_values($this->tableOfContents[$chapterIndex]['sections']);
    }

    public function generateSections($chapterIndex)
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


    public function create()
    {
        $this->validate();

        // Validate that at least one format is selected
        if (!$this->hasHardcopy && !$this->hasSoftcopy) {
            $this->addError('hasHardcopy', 'Please select at least one format (hardcopy or softcopy).');
            return;
        }

        // If softcopy is selected but no PDF file provided
        if ($this->hasSoftcopy && !$this->pdfFile) {
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
            // Handle cover image
            $coverPath = null;
            if ($this->coverImage) {
                $coverPath = $this->coverImage->store('book-covers', 'public');
            }

            // Handle PDF file
            $pdfPath = null;
            if ($this->pdfFile) {
                $pdfPath = $this->pdfFile->store('book-pdfs', 'public');
            }

            // Prepare table of contents
            $tocData = $this->showTableOfContents ? $this->tableOfContents : null;

            // Create book
            Book::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'author_id' => $this->authorId,
                'book_category_id' => $this->bookCategoryId,
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
            ]);

            DB::commit();
            session()->flash('message', 'Book created successfully!');
            return redirect()->route('admin.book-management');
        } catch (\Exception $e) {
            logError('Exception in create:'. $e);
            DB::rollback();
            $this->addError('general', 'Failed to create book. Please try again.');
        }
    }


    private function validateTableOfContents()
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


    public function cancel()
    {
        return redirect()->route('admin.book-management');
    }

    public function render()
    {
        return view('livewire.books.create-book');
    }
}
