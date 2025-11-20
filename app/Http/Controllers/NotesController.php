<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Book;
use App\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function index()
    {
        $notes = Note::where('user_id', Auth::id())
            ->orWhereHas('shares', function ($query) {
                $query->where('shared_with_user_id', Auth::id());
            })
            ->with(['book', 'academicSubject', 'user'])
            ->latest()
            ->paginate(10);

        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        $books = Book::all();
        $subjects = AcademicSubject::all();
        return view('notes.create', compact('books', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'book_id' => 'nullable|exists:books,id',
            'academic_subject_id' => 'nullable|exists:academic_subjects,id',
            'is_public' => 'boolean'
        ]);

        $note = Note::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'academic_subject_id' => $request->academic_subject_id,
            'is_public' => $request->boolean('is_public')
        ]);

        return redirect()->route('notes.show', $note)->with('success', 'Note created successfully.');
    }

    public function show(Note $note)
    {
        if (!$note->canUserView(Auth::id())) {
            abort(403);
        }

        $note->load(['book', 'academicSubject', 'user']);
        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        if (!$note->canUserEdit(Auth::id())) {
            abort(403);
        }

        $books = Book::all();
        $subjects = AcademicSubject::all();

        $note->load(['book', 'academicSubject']);
        return view('notes.edit', compact('note', 'books', 'subjects'));
    }

    public function update(Request $request, Note $note)
    {
        if (!$note->canUserEdit(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'book_id' => 'nullable|exists:books,id',
            'academic_subject_id' => 'nullable|exists:academic_subjects,id',
            'is_public' => 'boolean'
        ]);

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'book_id' => $request->book_id,
            'academic_subject_id' => $request->academic_subject_id,
            'is_public' => $request->boolean('is_public')
        ]);

        return redirect()->route('notes.show', $note)->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }

    public function share(Request $request, Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'can_edit' => 'boolean'
        ]);

        $note->shares()->updateOrCreate(
            ['shared_with_user_id' => $request->user_id],
            ['can_edit' => $request->boolean('can_edit')]
        );

        return back()->with('success', 'Note shared successfully.');
    }

    public function unshare(Note $note, User $user)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $note->shares()->where('shared_with_user_id', $user->id)->delete();
        return back()->with('success', 'Note unshared successfully.');
    }
}
