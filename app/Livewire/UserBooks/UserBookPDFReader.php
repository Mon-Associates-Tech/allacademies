<?php

namespace App\Livewire\UserBooks;

use App\Models\UserBook;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class UserBookPDFReader extends Component
{
    // Core properties
    public $bookId;

    public $book;

    public $currentPage = 1;

    public $totalPages = 0;

    public $isVisible = false;

    public $hasAccess = false;

    public $errorMessage = null;

    // UI state properties
    public $isLoading = false;

    public $scale = 1.2;

    public $isFullscreen = false;

    // Configuration properties
    public $autoSaveInterval = 30000; // 30 seconds

    public $progressSaveDelay = 5000; // 5 seconds after page change

    public $maxZoom = 3.0;

    public $minZoom = 0.5;

    public $zoomStep = 0.2;

    public function mount($bookId = null, array $config = []): void
    {
        // Apply configuration
        $this->autoSaveInterval = $config['autoSaveInterval'] ?? $this->autoSaveInterval;
        $this->progressSaveDelay = $config['progressSaveDelay'] ?? $this->progressSaveDelay;
        $this->maxZoom = $config['maxZoom'] ?? $this->maxZoom;
        $this->minZoom = $config['minZoom'] ?? $this->minZoom;
        $this->zoomStep = $config['zoomStep'] ?? $this->zoomStep;

        if ($bookId) {
            $this->bookId = $bookId;
            $this->loadBook();
        }
    }

    #[On('openUserBookPDFReader')]
    public function openReader(int $bookId, array $options = []): void
    {
        \Log::info('openUserBookPDFReader event received', ['bookId' => $bookId, 'options' => $options]);

        $this->reset(['errorMessage', 'isLoading']);
        $this->bookId = $bookId;
        $this->isLoading = true;

        try {
            \Log::info('About to load user book', ['bookId' => $this->bookId]);
            $this->loadBook();
            \Log::info('User book loaded successfully', [
                'hasAccess' => $this->hasAccess,
                'book_title' => $this->book->title ?? 'No title',
            ]);

            if ($this->hasAccess) {
                // Apply options
                if (isset($options['startPage'])) {
                    $this->currentPage = max(1, (int) $options['startPage']);
                }

                \Log::info('Setting isVisible to true and dispatching initializePDFReader');
                $this->isVisible = true;
                $this->logBookAccess();

                $dispatchData = [
                    'pdfUrl' => $this->book->content_url,
                    'currentPage' => $this->currentPage,
                    'bookId' => $this->bookId,
                    'scale' => $this->scale,
                    'book' => $this->book,
                    'config' => [
                        'autoSaveInterval' => $this->autoSaveInterval,
                        'progressSaveDelay' => $this->progressSaveDelay,
                        'maxZoom' => $this->maxZoom,
                        'minZoom' => $this->minZoom,
                        'zoomStep' => $this->zoomStep,
                    ],
                ];

                \Log::info('Dispatching initializePDFReader with data', $dispatchData);
                $this->dispatch('initializePDFReader', $dispatchData);
                \Log::info('initializePDFReader dispatched successfully');

            } else {
                \Log::warning('User does not have access to user book', [
                    'errorMessage' => $this->errorMessage,
                    'bookId' => $this->bookId,
                ]);
                $this->dispatch('show-error', [
                    'message' => $this->errorMessage ?? 'You do not have access to this book.',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Exception in openReader', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->errorMessage = 'Failed to open PDF reader: '.$e->getMessage();
            $this->dispatch('show-error', [
                'message' => $this->errorMessage,
            ]);
        } finally {
            $this->isLoading = false;
            \Log::info('openReader method completed', ['isVisible' => $this->isVisible]);
        }
    }

    #[On('closeUserBookPDFReader')]
    public function closeReader(): void
    {
        $this->isVisible = false;
        $this->dispatch('destroyPDFReader');
        $this->resetReaderState();
    }

    #[On('updatePageProgress')]
    public function handlePageProgress(int $currentPage = 0, int $totalPages = 0): void
    {
        // Livewire 3: payload keys are injected as individual named arguments.
        // JS must dispatch as: $dispatch('updatePageProgress', { currentPage: N, totalPages: N })
        if ($currentPage > 0 && $totalPages > 0) {
            $this->currentPage = $currentPage;
            $this->totalPages = $totalPages;
        }
    }

    #[On('updateReaderState')]
    public function handleReaderStateUpdate($scale = null, bool $isFullscreen = false): void
    {
        // Livewire 3: payload keys are injected as individual named arguments.
        // JS must dispatch as: $dispatch('updateReaderState', { scale: N, isFullscreen: bool })
        if ($scale !== null) {
            $this->scale = max($this->minZoom, min($this->maxZoom, (float) $scale));
        }

        $this->isFullscreen = $isFullscreen;
    }

    public function goToPage(int $pageNumber): void
    {
        if ($pageNumber >= 1 && $pageNumber <= $this->totalPages && $pageNumber !== $this->currentPage) {
            $this->dispatch('pdfGoToPage', ['page' => $pageNumber]);
        }
    }

    public function nextPage(): void
    {
        if ($this->currentPage < $this->totalPages) {
            $this->dispatch('pdfNextPage');
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->dispatch('pdfPreviousPage');
        }
    }

    // Zoom methods
    public function zoomIn(): void
    {
        $newScale = min($this->maxZoom, $this->scale + $this->zoomStep);
        if ($newScale !== $this->scale) {
            $this->scale = $newScale;
            $this->dispatch('pdfZoomIn');
        }
    }

    public function zoomOut(): void
    {
        $newScale = max($this->minZoom, $this->scale - $this->zoomStep);
        if ($newScale !== $this->scale) {
            $this->scale = $newScale;
            $this->dispatch('pdfZoomOut');
        }
    }

    public function fitToWidth(): void
    {
        $this->dispatch('pdfFitToWidth');
    }

    public function resetZoom(): void
    {
        $this->scale = 1.2;
        $this->dispatch('pdfResetZoom');
    }

    // Fullscreen methods
    public function toggleFullscreen(): void
    {
        $this->isFullscreen = ! $this->isFullscreen;
        $this->dispatch('pdfToggleFullscreen');
    }

    private function loadBook(): void
    {
        try {
            $this->book = UserBook::with(['user'])->findOrFail($this->bookId);
            \Log::info('User book loaded', [
                'book_id' => $this->book->id,
                'title' => $this->book->title,
                'content_url' => $this->book->content_url,
            ]);
            $this->checkAccess();
        } catch (\Exception $e) {
            \Log::error('Failed to load user book', [
                'bookId' => $this->bookId,
                'error' => $e->getMessage(),
            ]);
            $this->errorMessage = 'Book not found.';
            $this->hasAccess = false;
            throw $e;
        }
    }

    private function checkAccess(): void
    {
        $user = Auth::user();

        // Owner can always access their own books
        if ($this->book->user_id === $user->id) {
            $this->hasAccess = true;

            return;
        }

        // Check if book is shared with user
        $sharedAccess = $this->book->shares()
            ->where('shared_to_user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        if ($sharedAccess) {
            $this->hasAccess = true;

            return;
        }

        $this->hasAccess = false;
        $this->errorMessage = 'You do not have access to this book.';
    }

    private function logBookAccess(): void
    {
        try {
            activity()
                ->performedOn($this->book)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'opened_user_book_pdf_reader',
                    'book_id' => $this->bookId,
                    'book_title' => $this->book->title,
                    'starting_page' => $this->currentPage,
                ])
                ->log('User opened user book PDF reader');
        } catch (\Exception $e) {
            \Log::warning('Failed to log user book access', [
                'book_id' => $this->bookId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resetReaderState(): void
    {
        $this->reset([
            'bookId', 'book', 'currentPage', 'totalPages',
            'hasAccess', 'errorMessage', 'isLoading',
            'scale', 'isFullscreen',
        ]);
    }

    // Computed properties
    public function getProgressPercentage(): float|int
    {
        if ($this->totalPages <= 0) {
            return 0;
        }

        return round(($this->currentPage / $this->totalPages) * 100, 2);
    }

    public function getCanGoNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function getCanGoPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    public function getCanZoomIn(): bool
    {
        return $this->scale < $this->maxZoom;
    }

    public function getCanZoomOut(): bool
    {
        return $this->scale > $this->minZoom;
    }

    public function getScalePercentage(): float
    {
        return round($this->scale * 100);
    }

    public function getBookTitle()
    {
        return $this->book->title ?? 'Unknown Title';
    }

    public function getBookAuthor()
    {
        return $this->book->user->name ?? 'Unknown Author';
    }

    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        \Log::info('Rendering UserBookPDFReaderComponent', [
            'isVisible' => $this->isVisible,
            'hasAccess' => $this->hasAccess,
            'bookId' => $this->bookId,
        ]);

        return view('livewire.user-books.user-book-pdf-reader-modal', [
            'progressPercentage' => $this->getProgressPercentage(),
            'canGoNext' => $this->getCanGoNext(),
            'canGoPrevious' => $this->getCanGoPrevious(),
            'canZoomIn' => $this->getCanZoomIn(),
            'canZoomOut' => $this->getCanZoomOut(),
            'scalePercentage' => $this->getScalePercentage(),
            'bookTitle' => $this->getBookTitle(),
            'bookAuthor' => $this->getBookAuthor(),
        ]);
    }
}