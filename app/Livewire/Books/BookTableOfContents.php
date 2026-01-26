<?php

namespace App\Livewire\Books;

use App\Models\Book;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BookTableOfContents extends Component
{
    public Book $book;

    public $searchTerm = '';

    public $expandedChapters = [];

    public $viewMode = 'detailed'; // 'detailed' or 'compact'

    public $showPageNumbers = true;

    public $highlightedChapter = null;

    public $filteredChapters = [];

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->filteredChapters = $this->book->formatted_table_of_contents;

        // Auto-expand first chapter if it has sections
        if (! empty($this->filteredChapters) && ! empty($this->filteredChapters[0]['sections'])) {
            $this->expandedChapters[] = 1;
        }
    }

    public function render(): View
    {
        $this->filterChapters();

        return view('livewire.books.book-table-of-contents', [
            'chapters' => $this->filteredChapters,
            'totalChapters' => count($this->book->formatted_table_of_contents),
            'totalPages' => $this->book->pages,
            'estimatedReadingTime' => $this->book->estimated_reading_time,
        ]);
    }

    public function updatedSearchTerm()
    {
        $this->filterChapters();

        // Auto-expand chapters that match search
        if ($this->searchTerm) {
            $this->expandedChapters = collect($this->filteredChapters)
                ->pluck('chapter_number')
                ->toArray();
        } else {
            $this->expandedChapters = [];
        }
    }

    public function toggleChapter($chapterNumber)
    {
        if (in_array($chapterNumber, $this->expandedChapters)) {
            $this->expandedChapters = array_diff($this->expandedChapters, [$chapterNumber]);
        } else {
            $this->expandedChapters[] = $chapterNumber;
        }
    }

    public function expandAll()
    {
        $this->expandedChapters = collect($this->filteredChapters)
            ->pluck('chapter_number')
            ->toArray();
    }

    public function collapseAll()
    {
        $this->expandedChapters = [];
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function togglePageNumbers()
    {
        $this->showPageNumbers = ! $this->showPageNumbers;
    }

    public function jumpToChapter($chapterNumber)
    {
        $this->highlightedChapter = $chapterNumber;

        // Auto-expand if not already expanded
        if (! in_array($chapterNumber, $this->expandedChapters)) {
            $this->expandedChapters[] = $chapterNumber;
        }

        // Dispatch event to scroll to chapter
        $this->dispatch('scroll-to-chapter', chapterNumber: $chapterNumber);

        // Remove highlight after 3 seconds
        $this->dispatch('remove-highlight-after-delay');
    }

    public function getChapterProgress($chapter)
    {
        // This could be enhanced to show reading progress if user tracking is implemented
        return 0;
    }

    public function getEstimatedChapterReadingTime($chapter)
    {
        $pageCount = $chapter['page_count'] ?? 0;
        $wordsPerPage = 250;
        $wordsPerMinute = 250;

        if ($pageCount === 0) {
            return 'N/A';
        }

        $totalWords = $pageCount * $wordsPerPage;
        $readingTimeMinutes = $totalWords / $wordsPerMinute;

        if ($readingTimeMinutes < 1) {
            return '< 1 min';
        } elseif ($readingTimeMinutes < 60) {
            return round($readingTimeMinutes).' min';
        } else {
            $hours = floor($readingTimeMinutes / 60);
            $minutes = round($readingTimeMinutes % 60);

            if ($minutes === 0) {
                return $hours.'h';
            } else {
                return $hours.'h '.$minutes.'m';
            }
        }
    }

    private function filterChapters()
    {
        if (empty($this->searchTerm)) {
            $this->filteredChapters = $this->book->formatted_table_of_contents;

            return;
        }

        $searchTerm = strtolower($this->searchTerm);

        $this->filteredChapters = collect($this->book->formatted_table_of_contents)
            ->filter(function ($chapter) use ($searchTerm) {
                // Search in chapter title
                if (str_contains(strtolower($chapter['title']), $searchTerm)) {
                    return true;
                }

                // Search in chapter description
                if (str_contains(strtolower($chapter['description'] ?? ''), $searchTerm)) {
                    return true;
                }

                // Search in sections
                if (! empty($chapter['sections'])) {
                    foreach ($chapter['sections'] as $section) {
                        if (str_contains(strtolower($section), $searchTerm)) {
                            return true;
                        }
                    }
                }

                return false;
            })
            ->values()
            ->toArray();
    }

    public function clearSearch()
    {
        $this->searchTerm = '';
        $this->filteredChapters = $this->book->formatted_table_of_contents;
        $this->expandedChapters = [];
    }

    public function exportToc()
    {
        // Generate a text version of the table of contents
        $tocText = "Table of Contents - {$this->book->title}\n";
        $tocText .= str_repeat('=', 50)."\n\n";

        foreach ($this->book->formatted_table_of_contents as $chapter) {
            $tocText .= "Chapter {$chapter['chapter_number']}: {$chapter['title']}\n";

            if ($this->showPageNumbers && $chapter['page_range']) {
                $tocText .= "  {$chapter['page_range']}\n";
            }

            if (! empty($chapter['description'])) {
                $tocText .= "  {$chapter['description']}\n";
            }

            if (! empty($chapter['sections'])) {
                foreach ($chapter['sections'] as $section) {
                    $tocText .= "    • {$section}\n";
                }
            }

            $tocText .= "\n";
        }

        // Dispatch event to download the file
        $this->dispatch('download-toc', content: $tocText, filename: "toc-{$this->book->slug}.txt");
    }
}
