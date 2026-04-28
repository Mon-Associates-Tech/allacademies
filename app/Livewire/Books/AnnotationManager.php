<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\BookAnnotation;
use App\Models\BookAnnotationComment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AnnotationManager extends Component
{
    public $bookId;
    public $annotations = [];
    public $selectedAnnotation = null;
    public $commentText = '';
    public $showComments = false;

    protected $listeners = [
        'annotationCreated' => 'loadAnnotations',
        'annotationSelected' => 'selectAnnotation',
    ];

    public function mount($bookId)
    {
        $this->bookId = $bookId;
        $this->loadAnnotations();
    }

    public function loadAnnotations()
    {
        $this->annotations = BookAnnotation::query()
            ->where('book_id', $this->bookId)
            ->with(['user', 'comments.user', 'comments.replies.user'])
            ->withCount('comments')
            ->orderBy('page_number')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($annotation) => [
                'id' => $annotation->id,
                'page_number' => $annotation->page_number,
                'x_pct' => (float) $annotation->x_pct,
                'y_pct' => (float) $annotation->y_pct,
                'width_pct' => (float) $annotation->width_pct,
                'height_pct' => (float) $annotation->height_pct,
                'color' => $annotation->color,
                'user_name' => $annotation->user->name,
                'user_id' => $annotation->user_id,
                'comments_count' => $annotation->comments_count,
                'resolved_at' => $annotation->resolved_at?->toISOString(),
                'created_at' => $annotation->created_at->toISOString(),
            ])
            ->toArray();

        $this->dispatch('annotationsLoaded', annotations: $this->annotations);
    }

    public function selectAnnotation($annotationId)
    {
        $this->selectedAnnotation = BookAnnotation::with(['user', 'comments.user', 'comments.replies.user'])
            ->find($annotationId);
        $this->showComments = true;
    }

    public function addComment()
    {
        $this->validate([
            'commentText' => 'required|string|max:1000',
        ]);

        BookAnnotationComment::create([
            'book_annotation_id' => $this->selectedAnnotation->id,
            'user_id' => Auth::id(),
            'message' => $this->commentText,
        ]);

        $this->commentText = '';
        $this->selectAnnotation($this->selectedAnnotation->id);
        $this->loadAnnotations();
    }

    public function deleteComment($commentId)
    {
        $comment = BookAnnotationComment::find($commentId);
        
        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->hasAnyRole(['owner', 'admin']))) {
            $comment->delete();
            $this->selectAnnotation($this->selectedAnnotation->id);
            $this->loadAnnotations();
        }
    }

    public function resolveAnnotation($annotationId)
    {
        $annotation = BookAnnotation::find($annotationId);
        
        if ($annotation) {
            $annotation->update([
                'resolved_at' => now(),
                'resolved_by' => Auth::id(),
            ]);
            
            $this->loadAnnotations();
            $this->showComments = false;
            $this->selectedAnnotation = null;
        }
    }

    public function deleteAnnotation($annotationId)
    {
        $annotation = BookAnnotation::find($annotationId);
        
        if ($annotation && ($annotation->user_id === Auth::id() || Auth::user()->hasAnyRole(['owner', 'admin']))) {
            $annotation->delete();
            $this->loadAnnotations();
            $this->showComments = false;
            $this->selectedAnnotation = null;
        }
    }

    public function closeComments()
    {
        $this->showComments = false;
        $this->selectedAnnotation = null;
        $this->commentText = '';
    }

    public function render()
    {
        return view('livewire.books.annotation-manager');
    }
}
