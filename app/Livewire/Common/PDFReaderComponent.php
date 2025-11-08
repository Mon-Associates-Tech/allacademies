<?php

namespace App\Livewire\Common;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Book;
use App\Models\BookReadingProgress;
use App\Models\BookSubscription;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PDFReaderComponent extends Component
{
    // Core properties
    public $bookId;
    public $book;
    public $currentPage = 1;
    public $totalPages = 0;
    public $isVisible = false;
    public $userProgress;
    public $hasAccess = false;
    public $accessType = null;
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

    /**
     * @throws \Exception
     */
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

    #[On('openPDFReader')]
    public function openReader(int $bookId, array $options = []): void
    {

        $this->reset(['errorMessage', 'isLoading']);
        $this->bookId = $bookId;
        $this->isLoading = true;

        try {
            $this->loadBook();

            if ($this->hasAccess) {
                $this->loadUserProgress();

                // Apply options
                if (isset($options['startPage'])) {
                    $this->currentPage = max(1, (int) $options['startPage']);
                }

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
                    ]
                ];

                $this->dispatch('initializePDFReader', $dispatchData);

            } else {

                $this->dispatch('show-error', [
                    'message' => $this->errorMessage ?? 'You do not have access to this book.'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Exception in openReader', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->errorMessage = 'Failed to open PDF reader: ' . $e->getMessage();
            $this->dispatch('show-error', [
                'message' => $this->errorMessage
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    #[On('closePDFReader')]
    public function closeReader(): void
    {
        $this->saveProgressFinal();
        $this->isVisible = false;
        $this->dispatch('destroyPDFReader');
        $this->resetReaderState();
    }

    #[On('updatePageProgress')]
    public function handlePageProgress($data): void
    {
        $currentPage = $data['currentPage'] ?? $data[0] ?? null;
        $totalPages = $data['totalPages'] ?? $data[1] ?? null;
        $progressPercentage = $data['progressPercentage'] ?? $data[2] ?? null;

        if ($currentPage && $totalPages) {
            $this->currentPage = (int) $currentPage;
            $this->totalPages = (int) $totalPages;
            $this->saveProgress($this->currentPage, $this->totalPages);
        }
    }

    #[On('updateReaderState')]
    public function handleReaderStateUpdate($data): void
    {
        if (isset($data['scale'])) {
            $this->scale = max($this->minZoom, min($this->maxZoom, (float) $data['scale']));
        }

        if (isset($data['isFullscreen'])) {
            $this->isFullscreen = (bool) $data['isFullscreen'];
        }
    }

    #[On('pdfReaderError')]
    public function handleReaderError($error): void
    {
        $this->errorMessage = $error['message'] ?? 'PDF reader encountered an error';
        $this->isLoading = false;

        // Log error for debugging
        \Log::error('PDF Reader Error', [
            'book_id' => $this->bookId,
            'user_id' => Auth::id(),
            'error' => $error,
        ]);
    }

    // Navigation methods
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
        $this->isFullscreen = !$this->isFullscreen;
        $this->dispatch('pdfToggleFullscreen');
    }

    private function loadBook(): void
    {
        try {
            $this->book = Book::with(['author.user'])->findOrFail($this->bookId);
            $this->checkAccess();
        } catch (\Exception $e) {
            \Log::error('Failed to load book', [
                'bookId' => $this->bookId,
                'error' => $e->getMessage()
            ]);
            $this->errorMessage = 'Book not found.';
            $this->hasAccess = false;
            throw $e;
        }
    }

    private function checkAccess(): void
    {
        $user = Auth::user();
        $student = $user->student ?? $user;

        // Check if book is free
        if ($this->book->is_free || Auth::user()->role  === UserRole::OWNER || Auth::user()->role  === UserRole::ADMIN) {
            $this->hasAccess = true;
            $this->accessType = 'free';
            return;
        }

        // Check individual subscription
        $individualSubscription = BookSubscription::where('book_id', $this->bookId)
            ->where('user_id', $user->id)
            ->where('status', SubscriptionStatus::PAID->value)
            ->first();

        if ($individualSubscription) {
            $this->hasAccess = true;
            $this->accessType = 'individual';
            return;
        }

        // Check group subscription if student has a group
        if ($student && method_exists($student, 'studentGroup') && $student->studentGroup) {
            $groupSubscription = $student->studentGroup->groupBookSubscriptions()
                ->where('book_id', $this->bookId)
                ->where('status', SubscriptionStatus::PAID->value)
                ->first();

            if ($groupSubscription) {
                $this->hasAccess = true;
                $this->accessType = 'group';
                return;
            }
        }

        $this->hasAccess = false;
        $this->errorMessage = 'You need to subscribe to this book to access it.';
    }

    private function loadUserProgress(): void
    {
        $user = Auth::user();

        $this->userProgress = BookReadingProgress::where('book_id', $this->bookId)
            ->where('user_id', $user->id)
            ->first();

        if ($this->userProgress) {
            $this->currentPage = $this->userProgress->current_page;
            $this->totalPages = $this->userProgress->total_pages;
        }
    }

    private function saveProgress($currentPage, $totalPages): void
    {
        $user = Auth::user();

        try {
            BookReadingProgress::updateOrCreate(
                [
                    'book_id' => $this->bookId,
                    'user_id' => $user->id
                ],
                [
                    'current_page' => $currentPage,
                    'total_pages' => $totalPages,
                    'last_read_at' => now()
                ]
            );

            // Log reading activity (only for significant progress)
            if ($currentPage % 5 === 0 || $currentPage === 1 || $currentPage === $totalPages) {
                activity()
                    ->performedOn($this->book)
                    ->causedBy($user)
                    ->withProperties([
                        'action' => 'reading_progress_updated',
                        'current_page' => $currentPage,
                        'total_pages' => $totalPages,
                        'progress_percentage' => $totalPages > 0 ? round(($currentPage / $totalPages) * 100, 2) : 0,
                        'access_type' => $this->accessType
                    ])
                    ->log('Reading progress updated');
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to save reading progress', [
                'book_id' => $this->bookId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function saveProgressFinal(): void
    {
        if ($this->currentPage && $this->totalPages) {
            $this->saveProgress($this->currentPage, $this->totalPages);
        }
    }

    private function logBookAccess(): void
    {
        try {
            activity()
                ->performedOn($this->book)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'opened_pdf_reader',
                    'book_id' => $this->bookId,
                    'book_title' => $this->book->title,
                    'starting_page' => $this->currentPage,
                    'has_previous_progress' => $this->userProgress ? true : false,
                    'access_type' => $this->accessType
                ])
                ->log('User opened PDF reader');
        } catch (\Exception $e) {
            \Log::warning('Failed to log book access', [
                'book_id' => $this->bookId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function resetReaderState(): void
    {
        $this->reset([
            'bookId', 'book', 'currentPage', 'totalPages', 'userProgress',
            'hasAccess', 'accessType', 'errorMessage', 'isLoading',
            'scale', 'isFullscreen'
        ]);
    }

    // Computed properties
    public function getProgressPercentage(): float|int
    {
        if ($this->totalPages <= 0) return 0;
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
        return $this->book->author->user->name ?? 'Unknown Author';
    }

    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.common.PDFReaderModal', [
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
