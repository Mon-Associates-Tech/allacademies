<?php

namespace App\Livewire;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicResource;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\Note;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AcademicResourceManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Current view context
    public string $viewType = 'index'; // index, group, level, subject, topic, subtopic
    public ?int $contextId = null;
    public $contextModel = null;

    // Resource upload
    public $uploadFile;
    public string $resourceTitle = '';
    public string $resourceDescription = '';
    public bool $resourceIsPublic = false;

    // Note creation
    public string $noteTitle = '';
    public string $noteContent = '';
    public bool $noteIsPublic = false;

    // Todo creation
    public string $todoTitle = '';
    public string $todoDescription = '';
    public string $todoPriority = 'medium';
    public ?string $todoDueDate = null;
    public bool $todoIsPrivate = true;

    // Filters
    public string $todoFilter = 'all';
    public string $searchQuery = '';

    // Modal states
    public bool $showUploadModal = false;
    public bool $showNoteModal = false;
    public bool $showTodoModal = false;

    protected $listeners = [
        'refreshResources' => '$refresh',
    ];

    protected function rules(): array
    {
        return [
            'uploadFile' => 'required|file|max:102400', // 100MB
            'resourceTitle' => 'required|string|max:255',
            'resourceDescription' => 'nullable|string|max:1000',
            'resourceIsPublic' => 'boolean',
            'noteTitle' => 'required|string|max:255',
            'noteContent' => 'required|string',
            'noteIsPublic' => 'boolean',
            'todoTitle' => 'required|string|max:255',
            'todoDescription' => 'nullable|string|max:1000',
            'todoPriority' => 'required|in:low,medium,high',
            'todoDueDate' => 'nullable|date',
            'todoIsPrivate' => 'boolean',
        ];
    }

    public function mount(string $type = 'index', ?int $id = null): void
    {
        $this->viewType = $type;
        $this->contextId = $id;
        $this->loadContextModel();
    }

    protected function loadContextModel(): void
    {
        if (!$this->contextId) {
            return;
        }

        $this->contextModel = match ($this->viewType) {
            'group' => AcademicGroup::find($this->contextId),
            'level' => AcademicLevel::with('academicGroup')->find($this->contextId),
            'subject' => AcademicSubject::with('academicLevel.academicGroup')->find($this->contextId),
            'topic' => AcademicTopic::with('academicSubject.academicLevel.academicGroup')->find($this->contextId),
            'subtopic' => AcademicSubtopic::with('academicTopic.academicSubject.academicLevel.academicGroup')->find($this->contextId),
            default => null,
        };
    }

    public function getResourcesProperty()
    {
        if (!$this->contextModel) {
            return collect();
        }

        return $this->contextModel->resources()
            ->when($this->searchQuery, function ($query) {
                $query->where('title', 'like', "%{$this->searchQuery}%");
            })
            ->latest()
            ->paginate(12);
    }

    public function getNotesProperty()
    {
        if (!$this->contextModel) {
            return collect();
        }

        $user = Auth::user();

        return $this->contextModel->notes()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_public', true);
            })
            ->when($this->searchQuery, function ($query) {
                $query->where('title', 'like', "%{$this->searchQuery}%");
            })
            ->with('user')
            ->latest()
            ->get();
    }

    public function getTodosProperty()
    {
        if (!$this->contextModel) {
            return collect();
        }

        $user = Auth::user();

        $query = $this->contextModel->todos()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($subQ) use ($user) {
                        $subQ->where('is_private', false);
                    });
            })
            ->with('user');

        // Apply filters
        $query = match ($this->todoFilter) {
            'pending' => $query->where('status', 'pending'),
            'in_progress' => $query->where('status', 'in_progress'),
            'completed' => $query->where('status', 'completed'),
            'overdue' => $query->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->where('status', '!=', 'completed'),
            default => $query,
        };

        return $query->latest()->get();
    }

    public function getAcademicGroupsProperty()
    {
        if ($this->viewType !== 'index') {
            return collect();
        }

        return AcademicGroup::forCurrentSchool()
            ->with('academicLevels')
            ->get();
    }

    public function uploadResource(): void
    {
        $this->validate([
            'uploadFile' => 'required|file|max:102400',
            'resourceTitle' => 'required|string|max:255',
            'resourceDescription' => 'nullable|string|max:1000',
        ]);

        $file = $this->uploadFile;
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        // Determine file type
        $fileType = $this->determineFileType($extension, $mimeType);

        if (!$fileType) {
            $this->addError('uploadFile', 'Unsupported file type.');
            return;
        }

        // Store file
        $path = $file->store('academic-resources', 'public');

        // Create resource record
        AcademicResource::create([
            'title' => $this->resourceTitle,
            'description' => $this->resourceDescription,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'user_id' => Auth::id(),
            'resourceable_type' => get_class($this->contextModel),
            'resourceable_id' => $this->contextModel->id,
            'is_public' => $this->resourceIsPublic,
        ]);

        $this->reset(['uploadFile', 'resourceTitle', 'resourceDescription', 'resourceIsPublic']);
        $this->showUploadModal = false;
        $this->dispatch('notify', ['message' => 'Resource uploaded successfully!', 'type' => 'success']);
    }

    protected function determineFileType(string $extension, string $mimeType): ?string
    {
        $typeMap = [
            'pdf' => 'pdf',
            'doc' => 'doc',
            'docx' => 'docx',
            'xls' => 'xls',
            'xlsx' => 'xlsx',
            'ppt' => 'ppt',
            'pptx' => 'pptx',
            'txt' => 'txt',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image',
            'webp' => 'image',
        ];

        return $typeMap[$extension] ?? null;
    }

    public function createNote(): void
    {
        $this->validate([
            'noteTitle' => 'required|string|max:255',
            'noteContent' => 'required|string',
        ]);

        Note::create([
            'title' => $this->noteTitle,
            'content' => $this->noteContent,
            'user_id' => Auth::id(),
            'noteable_type' => get_class($this->contextModel),
            'noteable_id' => $this->contextModel->id,
            'is_public' => $this->noteIsPublic,
        ]);

        $this->reset(['noteTitle', 'noteContent', 'noteIsPublic']);
        $this->showNoteModal = false;
        $this->dispatch('notify', ['message' => 'Note created successfully!', 'type' => 'success']);
    }

    public function createTodo(): void
    {
        $this->validate([
            'todoTitle' => 'required|string|max:255',
            'todoDescription' => 'nullable|string|max:1000',
            'todoPriority' => 'required|in:low,medium,high',
            'todoDueDate' => 'nullable|date',
        ]);

        Todo::create([
            'title' => $this->todoTitle,
            'description' => $this->todoDescription,
            'user_id' => Auth::id(),
            'todoable_type' => get_class($this->contextModel),
            'todoable_id' => $this->contextModel->id,
            'priority' => $this->todoPriority,
            'due_date' => $this->todoDueDate,
            'is_private' => $this->todoIsPrivate,
        ]);

        $this->reset(['todoTitle', 'todoDescription', 'todoPriority', 'todoDueDate', 'todoIsPrivate']);
        $this->todoIsPrivate = true; // Reset to default
        $this->showTodoModal = false;
        $this->dispatch('notify', ['message' => 'Todo created successfully!', 'type' => 'success']);
    }

    public function toggleTodoStatus(int $todoId): void
    {
        $todo = Todo::find($todoId);

        if (!$todo || !$todo->canUserEdit(Auth::id())) {
            return;
        }

        if ($todo->is_completed) {
            $todo->markAsPending();
        } else {
            $todo->markAsCompleted();
        }
    }

    public function deleteTodo(int $todoId): void
    {
        $todo = Todo::find($todoId);

        if (!$todo || $todo->user_id !== Auth::id()) {
            return;
        }

        $todo->delete();
        $this->dispatch('notify', ['message' => 'Todo deleted successfully!', 'type' => 'success']);
    }

    public function deleteNote(int $noteId): void
    {
        $note = Note::find($noteId);

        if (!$note || $note->user_id !== Auth::id()) {
            return;
        }

        $note->delete();
        $this->dispatch('notify', ['message' => 'Note deleted successfully!', 'type' => 'success']);
    }

    public function deleteResource(int $resourceId): void
    {
        $resource = AcademicResource::find($resourceId);

        if (!$resource || $resource->user_id !== Auth::id()) {
            return;
        }

        // Delete file from storage
        Storage::disk('public')->delete($resource->file_path);

        $resource->delete();
        $this->dispatch('notify', ['message' => 'Resource deleted successfully!', 'type' => 'success']);
    }

    public function filterTodos(string $filter): void
    {
        $this->todoFilter = $filter;
    }

    public function exportNotes(string $format): StreamedResponse
    {
        $notes = $this->notes;

        return match ($format) {
            'pdf' => $this->exportNotesAsPdf($notes),
            'markdown' => $this->exportNotesAsMarkdown($notes),
            'text' => $this->exportNotesAsText($notes),
            default => $this->exportNotesAsText($notes),
        };
    }

    public function exportNote(int $noteId, string $format): StreamedResponse
    {
        $note = Note::findOrFail($noteId);

        return match ($format) {
            'pdf' => $this->exportNoteAsPdf($note),
            'markdown' => $this->exportNoteAsMarkdown($note),
            'text' => $this->exportNoteAsText($note),
            default => $this->exportNoteAsText($note),
        };
    }

    public function exportTodos(string $format): StreamedResponse
    {
        $todos = $this->todos;

        if ($format === 'csv') {
            return $this->exportTodosAsCsv($todos);
        }

        return $this->exportTodosAsCsv($todos);
    }

    protected function exportNotesAsPdf($notes): StreamedResponse
    {
        // For PDF export, we'll generate HTML and let the browser print
        $content = "<html><head><title>Notes Export</title></head><body>";
        $content .= "<h1>Notes Export</h1>";

        foreach ($notes as $note) {
            $content .= "<h2>{$note->title}</h2>";
            $content .= "<p><small>By {$note->user->name} on {$note->created_at->format('M d, Y')}</small></p>";
            $content .= "<div>{$note->content}</div>";
            $content .= "<hr>";
        }

        $content .= "</body></html>";

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'notes-export.html', ['Content-Type' => 'text/html']);
    }

    protected function exportNotesAsMarkdown($notes): StreamedResponse
    {
        $content = "# Notes Export\n\n";

        foreach ($notes as $note) {
            $content .= "## {$note->title}\n\n";
            $content .= "*By {$note->user->name} on {$note->created_at->format('M d, Y')}*\n\n";
            $content .= strip_tags($note->content) . "\n\n";
            $content .= "---\n\n";
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'notes-export.md', ['Content-Type' => 'text/markdown']);
    }

    protected function exportNotesAsText($notes): StreamedResponse
    {
        $content = "NOTES EXPORT\n";
        $content .= str_repeat("=", 50) . "\n\n";

        foreach ($notes as $note) {
            $content .= strtoupper($note->title) . "\n";
            $content .= "By {$note->user->name} on {$note->created_at->format('M d, Y')}\n\n";
            $content .= strip_tags($note->content) . "\n\n";
            $content .= str_repeat("-", 50) . "\n\n";
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'notes-export.txt', ['Content-Type' => 'text/plain']);
    }

    protected function exportNoteAsPdf(Note $note): StreamedResponse
    {
        $content = "<html><head><title>{$note->title}</title></head><body>";
        $content .= "<h1>{$note->title}</h1>";
        $content .= "<p><small>By {$note->user->name} on {$note->created_at->format('M d, Y')}</small></p>";
        $content .= "<div>{$note->content}</div>";
        $content .= "</body></html>";

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'note-' . $note->id . '.html', ['Content-Type' => 'text/html']);
    }

    protected function exportNoteAsMarkdown(Note $note): StreamedResponse
    {
        $content = "# {$note->title}\n\n";
        $content .= "*By {$note->user->name} on {$note->created_at->format('M d, Y')}*\n\n";
        $content .= strip_tags($note->content) . "\n";

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'note-' . $note->id . '.md', ['Content-Type' => 'text/markdown']);
    }

    protected function exportNoteAsText(Note $note): StreamedResponse
    {
        $content = strtoupper($note->title) . "\n";
        $content .= str_repeat("=", strlen($note->title)) . "\n\n";
        $content .= "By {$note->user->name} on {$note->created_at->format('M d, Y')}\n\n";
        $content .= strip_tags($note->content) . "\n";

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'note-' . $note->id . '.txt', ['Content-Type' => 'text/plain']);
    }

    protected function exportTodosAsCsv($todos): StreamedResponse
    {
        $headers = ['Title', 'Description', 'Priority', 'Status', 'Due Date', 'Created By', 'Created At', 'Completed At'];

        return response()->streamDownload(function () use ($todos, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($todos as $todo) {
                fputcsv($handle, [
                    $todo->title,
                    $todo->description ?? '',
                    ucfirst($todo->priority),
                    ucfirst(str_replace('_', ' ', $todo->status)),
                    $todo->due_date?->format('Y-m-d') ?? '',
                    $todo->user->name ?? 'Unknown',
                    $todo->created_at->format('Y-m-d H:i:s'),
                    $todo->completed_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        }, 'todos-export.csv', ['Content-Type' => 'text/csv']);
    }

    public function canUpload(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admins can always upload
        if ($user->hasRole(['admin', 'owner'])) {
            return true;
        }

        // Teachers can upload to their assigned areas
        if ($user->teacher) {
            return true;
        }

        return false;
    }

    public function canCreateNote(): bool
    {
        return Auth::check();
    }

    public function canCreateTodo(): bool
    {
        return Auth::check();
    }

    public function render()
    {
        return view('livewire.academic-resource-manager', [
            'resources' => $this->resources,
            'notes' => $this->notes,
            'todos' => $this->todos,
            'academicGroups' => $this->academicGroups,
            'canUpload' => $this->canUpload(),
            'canCreateNote' => $this->canCreateNote(),
            'canCreateTodo' => $this->canCreateTodo(),
        ]);
    }
}
