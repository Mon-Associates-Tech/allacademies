<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class BookNotesManager extends Component
{
    use WithPagination;

    public Book $book;

    public string $activeTab = 'book-notes';

    public string $searchTerm = '';

    public array $expandedNotes = [];

    public ?int $editingNoteId = null;

    public string $editingContent = '';

    public string $editingTitle = '';

    public string $newNoteTitle = '';

    public string $newNoteContent = '';

    public string $newNoteColor = 'white';

    public string $editingColor = 'white';

    protected $listeners = ['tabChanged' => 'handleTabChange'];

    public function mount(Book $book): void
    {
        $this->book = $book;
    }

    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
        $this->searchTerm = '';
        $this->expandedNotes = [];
    }

    public function handleTabChange(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->searchTerm = '';
        $this->expandedNotes = [];
    }

    public function toggleNote(int $noteId): void
    {
        if (in_array($noteId, $this->expandedNotes)) {
            $this->expandedNotes = array_filter($this->expandedNotes, fn ($id) => $id !== $noteId);
        } else {
            $this->expandedNotes[] = $noteId;
        }
    }

    public function isNoteExpanded(int $noteId): bool
    {
        return in_array($noteId, $this->expandedNotes);
    }

    #[Computed]
    public function bookNotes()
    {
        $query = Note::where('book_id', $this->book->id)
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');

        if (! empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('content', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('title', 'like', '%'.$this->searchTerm.'%');
            });
        }

        return $query->paginate(10, pageName: 'bookNotesPage');
    }

    #[Computed]
    public function userNotes()
    {
        $query = Note::where('user_id', Auth::id())
            ->with('book')
            ->orderBy('updated_at', 'desc');

        if (! empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('content', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('title', 'like', '%'.$this->searchTerm.'%')
                    ->orWhereHas('book', function ($bookQuery) {
                        $bookQuery->where('title', 'like', '%'.$this->searchTerm.'%');
                    });
            });
        }

        return $query->paginate(10, pageName: 'userNotesPage');
    }

    public function saveNote(string $content, string $title = ''): void
    {
        if (empty($content)) {
            $this->dispatch('notify', ['message' => 'Note content is required', 'type' => 'error']);

            return;
        }

        Note::create([
            'title' => ! empty($title) ? $title : substr(strip_tags($content), 0, 50).'...',
            'content' => $content,
            'user_id' => Auth::id(),
            'book_id' => $this->book->id,
            'background_color' => $this->newNoteColor,
        ]);

        $this->newNoteTitle = '';
        $this->newNoteContent = '';
        $this->newNoteColor = 'white';
        $this->dispatch('notify', ['message' => 'Note saved successfully!', 'type' => 'success']);
        $this->dispatch('clear-editor-newNoteContent');

        // Log activity
        activity()
            ->performedOn($this->book)
            ->causedBy(Auth::user())
            ->withProperties(['action' => 'created_note', 'book_id' => $this->book->id])
            ->log('User created a note for book');
    }

    public function editNote(int $noteId): void
    {
        $note = Note::where('id', $noteId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->editingNoteId = $note->id;
        $this->editingContent = $note->content;
        $this->editingTitle = $note->title;
        $this->editingColor = $note->background_color ?? 'white';
    }

    public function updateNote(string $content, string $title = ''): void
    {
        if (empty($content)) {
            $this->dispatch('notify', ['message' => 'Note content is required', 'type' => 'error']);

            return;
        }

        $note = Note::where('id', $this->editingNoteId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $note->update([
            'content' => $content,
            'title' => ! empty($title) ? $title : substr(strip_tags($content), 0, 50).'...',
            'background_color' => $this->editingColor,
        ]);

        $this->editingNoteId = null;
        $this->editingContent = '';
        $this->editingTitle = '';
        $this->editingColor = 'white';
        $this->dispatch('notify', ['message' => 'Note updated successfully!', 'type' => 'success']);

        // Log activity
        activity()
            ->performedOn($note)
            ->causedBy(Auth::user())
            ->withProperties(['action' => 'updated_note', 'note_id' => $note->id])
            ->log('User updated a note');
    }

    public function deleteNote(int $noteId): void
    {
        $note = Note::where('id', $noteId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $note->delete();
        $this->dispatch('notify', ['message' => 'Note deleted successfully!', 'type' => 'success']);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['action' => 'deleted_note', 'note_id' => $noteId])
            ->log('User deleted a note');
    }

    public function cancelEdit(): void
    {
        $this->editingNoteId = null;
        $this->editingContent = '';
        $this->editingTitle = '';
        $this->editingColor = 'white';
    }

    public function render()
    {
        return view('livewire.books.book-notes-manager');
    }
}
