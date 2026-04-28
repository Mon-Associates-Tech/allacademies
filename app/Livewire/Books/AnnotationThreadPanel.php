<?php

namespace App\Livewire\Books;

use App\Models\BookAnnotation;
use App\Models\BookAnnotationComment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class AnnotationThreadPanel extends Component
{
    public int $bookId;

    public ?int $selectedAnnotationId = null;

    public ?BookAnnotation $selectedAnnotation = null;

    public string $newComment = '';

    public string $replyMessage = '';

    public ?int $replyingToCommentId = null;

    public string $editingMessage = '';

    public ?int $editingCommentId = null;

    public function mount(int $bookId): void
    {
        $this->bookId = $bookId;
        $this->dispatchAnnotations();
    }

    #[On('book-annotation-create')]
    public function createAnnotation($payload = []): void
    {
        $data = $this->normalizePayload($payload);
        if ((int) ($data['bookId'] ?? 0) !== $this->bookId) {
            return;
        }

        $validated = validator($data, [
            'pageNumber' => 'required|integer|min:1',
            'xPct' => 'required|numeric|min:0|max:100',
            'yPct' => 'required|numeric|min:0|max:100',
            'widthPct' => 'required|numeric|min:0|max:100',
            'heightPct' => 'required|numeric|min:0|max:100',
            'color' => 'nullable|string|max:20',
        ])->validate();

        $annotation = BookAnnotation::query()->create([
            'book_id' => $this->bookId,
            'user_id' => Auth::id(),
            'page_number' => (int) $validated['pageNumber'],
            'x_pct' => $validated['xPct'],
            'y_pct' => $validated['yPct'],
            'width_pct' => $validated['widthPct'],
            'height_pct' => $validated['heightPct'],
            'color' => $validated['color'] ?? '#f59e0b',
        ]);

        $this->dispatchAnnotations();
    }

    #[On('book-annotation-selected')]
    public function selectAnnotation($payload = []): void
    {
        $data = $this->normalizePayload($payload);
        $annotationId = (int) ($data['annotationId'] ?? 0);

        $annotation = BookAnnotation::query()
            ->where('book_id', $this->bookId)
            ->whereKey($annotationId)
            ->first();

        if (! $annotation) {
            return;
        }

        $this->selectedAnnotationId = $annotation->id;
        $this->loadSelectedAnnotation();
    }

    #[On('book-annotation-delete')]
    public function deleteAnnotation($payload = []): void
    {
        $data = $this->normalizePayload($payload);
        $annotationId = (int) ($data['annotationId'] ?? 0);

        $annotation = BookAnnotation::query()
            ->where('book_id', $this->bookId)
            ->whereKey($annotationId)
            ->first();

        if (! $annotation || ! $this->canManageAnnotation($annotation)) {
            return;
        }

        $annotation->delete();

        if ($this->selectedAnnotationId === $annotationId) {
            $this->resetSelectedThread();
        }

        $this->dispatchAnnotations();
    }

    public function addComment(): void
    {
        if (! $this->selectedAnnotationId) {
            return;
        }

        $this->validate([
            'newComment' => 'required|string|min:1|max:2000',
        ]);

        BookAnnotationComment::query()->create([
            'book_annotation_id' => $this->selectedAnnotationId,
            'user_id' => Auth::id(),
            'message' => trim($this->newComment),
        ]);

        $this->newComment = '';
        $this->loadSelectedAnnotation();
        $this->dispatchAnnotations();
    }

    public function startReply(int $commentId): void
    {
        $this->replyingToCommentId = $commentId;
        $this->replyMessage = '';
    }

    public function cancelReply(): void
    {
        $this->replyingToCommentId = null;
        $this->replyMessage = '';
    }

    public function addReply(): void
    {
        if (! $this->selectedAnnotationId || ! $this->replyingToCommentId) {
            return;
        }

        $this->validate([
            'replyMessage' => 'required|string|min:1|max:2000',
        ]);

        BookAnnotationComment::query()->create([
            'book_annotation_id' => $this->selectedAnnotationId,
            'user_id' => Auth::id(),
            'parent_id' => $this->replyingToCommentId,
            'message' => trim($this->replyMessage),
        ]);

        $this->cancelReply();
        $this->loadSelectedAnnotation();
        $this->dispatchAnnotations();
    }

    public function startEditing(int $commentId): void
    {
        $comment = BookAnnotationComment::query()
            ->where('book_annotation_id', $this->selectedAnnotationId)
            ->whereKey($commentId)
            ->first();

        if (! $comment || ! $this->canEditComment($comment)) {
            return;
        }

        $this->editingCommentId = $comment->id;
        $this->editingMessage = $comment->message;
    }

    public function cancelEditing(): void
    {
        $this->editingCommentId = null;
        $this->editingMessage = '';
    }

    public function saveCommentEdit(): void
    {
        if (! $this->editingCommentId) {
            return;
        }

        $this->validate([
            'editingMessage' => 'required|string|min:1|max:2000',
        ]);

        $comment = BookAnnotationComment::query()
            ->where('book_annotation_id', $this->selectedAnnotationId)
            ->whereKey($this->editingCommentId)
            ->first();

        if (! $comment || ! $this->canEditComment($comment)) {
            return;
        }

        $comment->update([
            'message' => trim($this->editingMessage),
            'edited_at' => now(),
        ]);

        $this->cancelEditing();
        $this->loadSelectedAnnotation();
    }

    public function resolveAnnotation(): void
    {
        if (! $this->selectedAnnotation || ! $this->canManageAnnotation($this->selectedAnnotation)) {
            return;
        }

        $this->selectedAnnotation->update([
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        $this->loadSelectedAnnotation();
        $this->dispatchAnnotations();
    }

    public function reopenAnnotation(): void
    {
        if (! $this->selectedAnnotation || ! $this->canManageAnnotation($this->selectedAnnotation)) {
            return;
        }

        $this->selectedAnnotation->update([
            'resolved_at' => null,
            'resolved_by' => null,
        ]);

        $this->loadSelectedAnnotation();
        $this->dispatchAnnotations();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = BookAnnotationComment::query()
            ->where('book_annotation_id', $this->selectedAnnotationId)
            ->whereKey($commentId)
            ->first();

        if (! $comment || ! $this->canEditComment($comment)) {
            return;
        }

        $comment->delete();
        $this->loadSelectedAnnotation();
        $this->dispatchAnnotations();
    }

    private function loadSelectedAnnotation(): void
    {
        if (! $this->selectedAnnotationId) {
            $this->selectedAnnotation = null;

            return;
        }

        $this->selectedAnnotation = BookAnnotation::query()
            ->where('book_id', $this->bookId)
            ->whereKey($this->selectedAnnotationId)
            ->with([
                'user:id,name',
                'comments' => fn ($query) => $query
                    ->whereNull('parent_id')
                    ->orderBy('created_at')
                    ->with([
                        'user:id,name',
                        'replies' => fn ($replyQuery) => $replyQuery
                            ->orderBy('created_at')
                            ->with('user:id,name'),
                    ]),
            ])
            ->first();
    }

    private function resetSelectedThread(): void
    {
        $this->selectedAnnotationId = null;
        $this->selectedAnnotation = null;
        $this->newComment = '';
        $this->cancelReply();
        $this->cancelEditing();
    }

    private function dispatchAnnotations(): void
    {
        $annotations = BookAnnotation::query()
            ->where('book_id', $this->bookId)
            ->withCount('comments')
            ->get([
                'id',
                'book_id',
                'user_id',
                'page_number',
                'x_pct',
                'y_pct',
                'width_pct',
                'height_pct',
                'color',
                'resolved_at',
            ])
            ->map(fn (BookAnnotation $annotation) => [
                'id' => $annotation->id,
                'book_id' => $annotation->book_id,
                'user_id' => $annotation->user_id,
                'page_number' => $annotation->page_number,
                'x_pct' => (float) $annotation->x_pct,
                'y_pct' => (float) $annotation->y_pct,
                'width_pct' => (float) $annotation->width_pct,
                'height_pct' => (float) $annotation->height_pct,
                'color' => $annotation->color,
                'resolved_at' => $annotation->resolved_at?->toISOString(),
                'comments_count' => $annotation->comments_count,
            ])
            ->values()
            ->all();

        $this->dispatch('book-annotations-updated', [
            'annotations' => $annotations,
            'bookId' => $this->bookId,
        ]);
    }

    private function normalizePayload($payload): array
    {
        if (is_array($payload) && isset($payload[0]) && is_array($payload[0])) {
            return $payload[0];
        }

        if (is_array($payload)) {
            return $payload;
        }

        return [];
    }

    public function canManageAnnotation(?BookAnnotation $annotation): bool
    {
        if (! $annotation) {
            return false;
        }

        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return (int) $annotation->user_id === (int) $user->id || $user->hasAnyRole(['owner', 'admin']);
    }

    public function canEditComment(BookAnnotationComment $comment): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return (int) $comment->user_id === (int) $user->id || $user->hasAnyRole(['owner', 'admin']);
    }

    public function render()
    {
        return view('livewire.books.annotation-thread-panel');
    }
}
