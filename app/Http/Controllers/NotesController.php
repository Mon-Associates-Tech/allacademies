<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubject;
use App\Models\Book;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\User;
use App\Services\NoteExportService;
use App\Services\NoteShareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function __construct(
        protected NoteShareService $shareService,
        protected NoteExportService $exportService
    ) {}

    public function index(Request $request)
    {
        $query = Note::query();

        // Base query - user's own notes or shared notes
        $query->where(function ($q) {
            $q->where('user_id', Auth::id())
                ->orWhereHas('shares', function ($shareQuery) {
                    $shareQuery->where('shared_with_user_id', Auth::id());
                });
        });

        // Filter by ownership type
        if ($request->filled('ownership')) {
            if ($request->ownership === 'my_notes') {
                $query->where('user_id', Auth::id());
            } elseif ($request->ownership === 'shared_with_me') {
                $query->where('user_id', '!=', Auth::id())
                    ->whereHas('shares', function ($q) {
                        $q->where('shared_with_user_id', Auth::id());
                    });
            }
        }

        // Filter by book
        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        // Filter by academic subject
        if ($request->filled('subject_id')) {
            $query->where('academic_subject_id', $request->subject_id);
        }

        // Filter by visibility
        if ($request->filled('visibility')) {
            if ($request->visibility === 'public') {
                $query->where('is_public', true);
            } elseif ($request->visibility === 'private') {
                $query->where('is_public', false);
            }
        }

        // Search by title or content
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            case 'updated_at':
                $query->orderBy('updated_at', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        // Eager load relationships
        $query->with(['book', 'academicSubject', 'user', 'shares']);

        // Paginate with 12 items per page
        $notes = $query->paginate(12)->appends($request->query());

        // Get filter options
        $books = Book::whereHas('notes', function ($q) {
            $q->where('user_id', Auth::id())
                ->orWhereHas('shares', function ($shareQuery) {
                    $shareQuery->where('shared_with_user_id', Auth::id());
                });
        })->orderBy('title')->get();

        $subjects = AcademicSubject::whereHas('notes', function ($q) {
            $q->where('user_id', Auth::id())
                ->orWhereHas('shares', function ($shareQuery) {
                    $shareQuery->where('shared_with_user_id', Auth::id());
                });
        })->orderBy('name')->get();

        // Get active filters for display
        $activeFilters = $this->getActiveFilters($request);

        return view('notes.index', compact('notes', 'books', 'subjects', 'activeFilters'));
    }

    private function getActiveFilters(Request $request): array
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }

        if ($request->filled('ownership')) {
            $filters['ownership'] = $request->ownership === 'my_notes' ? 'My Notes' : 'Shared with Me';
        }

        if ($request->filled('book_id')) {
            $book = Book::find($request->book_id);
            if ($book) {
                $filters['book'] = $book->title;
            }
        }

        if ($request->filled('subject_id')) {
            $subject = AcademicSubject::find($request->subject_id);
            if ($subject) {
                $filters['subject'] = $subject->name;
            }
        }

        if ($request->filled('visibility')) {
            $filters['visibility'] = ucfirst($request->visibility);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $dateRange = '';
            if ($request->filled('date_from')) {
                $dateRange .= date('M d, Y', strtotime($request->date_from));
            }
            if ($request->filled('date_to')) {
                $dateRange .= ' - '.date('M d, Y', strtotime($request->date_to));
            }
            $filters['date_range'] = $dateRange;
        }

        return $filters;
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
            'is_public' => 'boolean',
        ]);

        $note = Note::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'academic_subject_id' => $request->academic_subject_id,
            'is_public' => $request->boolean('is_public'),
        ]);

        return redirect()->route('notes.show', $note)->with('success', 'Note created successfully.');
    }

    public function show(Note $note)
    {
        if (! $note->canUserView(Auth::id())) {
            abort(403);
        }

        $note->load(['book', 'academicSubject', 'user']);

        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        if (! $note->canUserEdit(Auth::id())) {
            abort(403);
        }

        $books = Book::all();
        $subjects = AcademicSubject::all();

        $note->load(['book', 'academicSubject']);

        return view('notes.edit', compact('note', 'books', 'subjects'));
    }

    public function update(Request $request, Note $note)
    {
        if (! $note->canUserEdit(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'book_id' => 'nullable|exists:books,id',
            'academic_subject_id' => 'nullable|exists:academic_subjects,id',
            'is_public' => 'boolean',
        ]);

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'book_id' => $request->book_id,
            'academic_subject_id' => $request->academic_subject_id,
            'is_public' => $request->boolean('is_public'),
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
            'share_type' => 'required|in:individual,academic_group,academic_level,student_group,school_wide',
            'recipient_ids' => 'required|array|min:1',
            'recipient_ids.*' => 'required|integer',
            'can_edit' => 'boolean',
        ]);

        $result = $this->shareService->shareNote(
            $note,
            $request->share_type,
            $request->recipient_ids,
            $request->boolean('can_edit')
        );

        return back()->with('success', "Note shared with {$result['users_notified']} ".
            \Str::plural('recipient', $result['users_notified']).' successfully.');
    }

    public function unshare(Note $note, Request $request)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'share_type' => 'required|string',
            'identifier' => 'required',
        ]);

        $this->shareService->unshare($note, $request->share_type, $request->identifier);

        return back()->with('success', 'Note access removed successfully.');
    }

    public function download(Note $note, Request $request)
    {
        // Check permissions
        if (! $note->canUserView(Auth::id())) {
            abort(403, 'You do not have permission to download this note.');
        }

        $format = $request->get('format', 'pdf');

        // Validate format
        if (! in_array($format, ['pdf', 'txt', 'docx'])) {
            return back()->with('error', 'Invalid export format.');
        }

        try {
            $result = $this->exportService->export($note, $format);

            if (! $result['success']) {
                return back()->with('error', $result['error']);
            }

            return response($result['content'])
                ->header('Content-Type', $result['mime_type'])
                ->header('Content-Disposition', 'attachment; filename="'.$result['filename'].'"');

        } catch (\Exception $e) {
            \Log::error('Note download failed', [
                'note_id' => $note->id,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to download note. Please try again.');
        }
    }

    public function downloadAttachment(Note $note, NoteAttachment $attachment)
    {
        // Check permissions
        if (! $note->canUserView(Auth::id())) {
            abort(403, 'You do not have permission to access this note.');
        }

        // Verify attachment belongs to note
        if ($attachment->note_id !== $note->id) {
            abort(404);
        }

        $filePath = storage_path('app/public/'.$attachment->path);

        if (! file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $attachment->original_filename);
    }

    public function viewAttachment(Note $note, NoteAttachment $attachment)
    {
        // Check permissions
        if (! $note->canUserView(Auth::id())) {
            abort(403, 'You do not have permission to access this note.');
        }

        // Verify attachment belongs to note
        if ($attachment->note_id !== $note->id) {
            abort(404);
        }

        $filePath = storage_path('app/public/'.$attachment->path);

        if (! file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => $attachment->mime_type,
            'Content-Disposition' => 'inline; filename="'.$attachment->original_filename.'"',
        ]);
    }
}
