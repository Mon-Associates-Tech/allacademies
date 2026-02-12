<?php

namespace App\Livewire\Resources;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EducationalResource;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ResourceUpload extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public string $tagsInput = '';

    public $file;

    public ?int $academicGroupId = null;

    public ?int $academicLevelId = null;

    public ?int $academicSubjectId = null;

    /** @var array<int> */
    public array $selectedTopics = [];

    /** @var array<int> */
    public array $selectedSubtopics = [];

    public bool $isSchoolScoped = false;

    public bool $showSuccess = false;

    public ?EducationalResource $createdResource = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'tagsInput' => 'nullable|string|max:500',
            'file' => 'required|file|max:102400', // 100MB max
            'academicGroupId' => 'required|exists:academic_groups,id',
            'academicLevelId' => 'required|exists:academic_levels,id',
            'academicSubjectId' => 'required|exists:academic_subjects,id',
            'selectedTopics' => 'nullable|array',
            'selectedTopics.*' => 'exists:academic_topics,id',
            'selectedSubtopics' => 'nullable|array',
            'selectedSubtopics.*' => 'exists:academic_subtopics,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Please enter a title for the resource.',
            'file.required' => 'Please select a file to saveResource.',
            'file.max' => 'The file size must not exceed 100MB.',
            'academicGroupId.required' => 'Please select an academic group.',
            'academicLevelId.required' => 'Please select an academic level.',
            'academicSubjectId.required' => 'Please select a subject.',
        ];
    }

    public function mount(): void
    {
        // Check if user can saveResource resources
        $user = auth()->user();
        if (! $this->canUpload($user)) {
            abort(403, 'You do not have permission to saveResource resources.');
        }
    }

    public function updatedAcademicGroupId(): void
    {
        $this->academicLevelId = null;
        $this->academicSubjectId = null;
        $this->selectedTopics = [];
        $this->selectedSubtopics = [];
    }

    public function updatedAcademicLevelId(): void
    {
        $this->academicSubjectId = null;
        $this->selectedTopics = [];
        $this->selectedSubtopics = [];
    }

    public function updatedAcademicSubjectId(): void
    {
        $this->selectedTopics = [];
        $this->selectedSubtopics = [];
    }

    public function updatedSelectedTopics(): void
    {
        // Filter subtopics to only include those belonging to selected topics
        if (! empty($this->selectedSubtopics)) {
            $validSubtopicIds = AcademicSubtopic::whereIn('academic_topic_id', $this->selectedTopics)
                ->pluck('id')
                ->toArray();
            $this->selectedSubtopics = array_intersect($this->selectedSubtopics, $validSubtopicIds);
        }
    }

    public function saveResource(): void
    {
        $this->validate();

        $user = auth()->user();

        // Determine file format based on mime type
        $mimeType = $this->file->getMimeType();
        $format = $this->determineFormat($mimeType);

        // Generate unique file name
        $originalName = $this->file->getClientOriginalName();
        $extension = $this->file->getClientOriginalExtension();
        $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'_'.time().'.'.$extension;

        // Store file
        $path = $this->file->storeAs('educational-resources', $fileName, 'public');

        // Parse tags
        $tags = $this->parseTags($this->tagsInput);

        // Determine school scoping
        $schoolId = null;
        if ($this->isSchoolScoped && $user->school_id) {
            $schoolId = $user->school_id;
        }

        // Create resource
        $resource = EducationalResource::create([
            'title' => $this->title,
            'description' => $this->description,
            'tags' => $tags,
            'file_path' => $path,
            'file_name' => $originalName,
            'file_type' => $mimeType,
            'format' => $format,
            'file_size' => $this->file->getSize(),
            'academic_subject_id' => $this->academicSubjectId,
            'school_id' => $schoolId,
            'uploaded_by' => $user->id,
        ]);

        // Attach topics and subtopics
        if (! empty($this->selectedTopics)) {
            $resource->topics()->attach($this->selectedTopics);
        }

        if (! empty($this->selectedSubtopics)) {
            $resource->subtopics()->attach($this->selectedSubtopics);
        }

        $this->createdResource = $resource;
        $this->showSuccess = true;

        // Reset form
        $this->reset(['title', 'description', 'tagsInput', 'file', 'selectedTopics', 'selectedSubtopics']);

        $this->dispatch('resource-uploaded', resourceId: $resource->id);
    }

    public function uploadAnother(): void
    {
        $this->showSuccess = false;
        $this->createdResource = null;
    }

    protected function determineFormat(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        return 'text';
    }

    /**
     * @return array<string>
     */
    protected function parseTags(string $tagsInput): array
    {
        if (empty($tagsInput)) {
            return [];
        }

        return array_filter(
            array_map('trim', explode(',', $tagsInput)),
            fn ($tag) => ! empty($tag)
        );
    }

    protected function canUpload($user): bool
    {
        if (! $user) {
            return false;
        }

        // Administrators, owners, and teachers can upload
        return $user->hasAnyRole(['admin', 'owner', 'teacher', 'super-admin', 'moderator']);
    }

    public function render()
    {
        // Show all academic groups without school restriction
        $academicGroups = AcademicGroup::query()
            ->orderBy('name')
            ->get();

        $academicLevels = $this->academicGroupId
            ? AcademicLevel::where('academic_group_id', $this->academicGroupId)->orderBy('name')->get()
            : collect();

        $academicSubjects = $this->academicLevelId
            ? AcademicSubject::where('academic_level_id', $this->academicLevelId)->orderBy('name')->get()
            : collect();

        $topics = $this->academicSubjectId
            ? AcademicTopic::where('academic_subject_id', $this->academicSubjectId)->orderBy('name')->get()
            : collect();

        $subtopics = ! empty($this->selectedTopics)
            ? AcademicSubtopic::whereIn('academic_topic_id', $this->selectedTopics)->orderBy('name')->get()
            : collect();

        return view('livewire.resources.resource-upload', [
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'academicSubjects' => $academicSubjects,
            'topics' => $topics,
            'subtopics' => $subtopics,
        ]);
    }
}
