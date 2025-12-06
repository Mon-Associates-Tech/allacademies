<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookNotesManager extends Component
{
    public Book $book;
    public $activeTab = 'book-notes';
    public $bookNotes = [];
    public $userNotes = [];
    public $newNoteContent = '';
    public $editingNoteId = null;
    public $editingContent = '';

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->loadNotes();
    }

    public function loadNotes()
    {
        // Load notes for the current book
        $this->bookNotes = Note::where('book_id', $this->book->id)
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        // Load all user notes
        $this->userNotes = Note::where('user_id', Auth::id())
            ->with('book')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function saveNote()
    {
        $this->validate([
            'newNoteContent' => 'required|string|max:10000'
        ]);

        Note::create([
            'title' => substr($this->newNoteContent, 0, 50) . '...',
            'content' => $this->newNoteContent,
            'user_id' => Auth::id(),
            'book_id' => $this->book->id,
        ]);

        $this->newNoteContent = '';
        $this->loadNotes();
        $this->dispatch('notify', ['message' => 'Note saved successfully!']);
    }

    public function editNote(Note $note)
    {
        $this->editingNoteId = $note->id;
        $this->editingContent = $note->content;
    }

    public function updateNote()
    {
        $this->validate([
            'editingContent' => 'required|string|max:10000'
        ]);

        $note = Note::where('id', $this->editingNoteId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $note->update([
            'content' => $this->editingContent
        ]);

        $this->editingNoteId = null;
        $this->editingContent = '';
        $this->loadNotes();
        $this->dispatch('notify', ['message' => 'Note updated successfully!']);
    }

    public function deleteNote(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            return;
        }

        $note->delete();
        $this->loadNotes();
        $this->dispatch('notify', ['message' => 'Note deleted successfully!']);
    }

    public function cancelEdit()
    {
        $this->editingNoteId = null;
        $this->editingContent = '';
    }

    public function render()
    {
        return view('livewire.books.book-notes-manager');
    }
}

