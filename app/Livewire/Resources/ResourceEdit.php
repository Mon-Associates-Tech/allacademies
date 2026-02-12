<?php

namespace App\Livewire\Resources;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EducationalResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ResourceEdit extends Component
{
    use WithFileUploads;

    public EducationalResource $resource;

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

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'tagsInput' => 'nullable|string|max:500',
            'file' => 'nullable|file|max:102400', // 100MB max, optional for edit
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
            'file.max' => 'The file size must not exceed 100MB.',
            'academicGroupId.required' => 'Please select an academic group.',
            'academicLevelId.required' => 'Please select an academic level.',
            'academicSubjectId.required' => 'Please select a subject.',
        ];
    }

    public function mount(EducationalResource $resource): void
    {
        $user = auth()->user();

        // Check if user can edit this resource
        if (! $this->canEdit($user, $resource)) {
            abort(403, 'You do not have permission to edit this resource.');
        }

        $this->resource = $resource;

        // Load existing data
        $this->title = $resource->title;
        $this->description = $resource->description ?? '';
        $this->tagsInput = $resource->tags ? implode(', ', $resource->tags) : '';
        $this->academicSubjectId = $resource->academic_subject_id;
        $this->isSchoolScoped = $resource->school_id !== null;

        // Load academic hierarchy
        if ($resource->academicSubject) {
            $this->academicLevelId = $resource->academicSubject->academic_level_id;

            if ($resource->academicSubject->academicLevel) {
                $this->academicGroupId = $resource->academicSubject->academicLevel->academic_group_id;
            }
        }

        // Load selected topics and subtopics
        $this->selectedTopics = $resource->topics->pluck('id')->toArray();
        $this->selectedSubtopics = $resource->subtopics->pluck('id')->toArray();
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

    public function updateResource(): void
    {
        $this->validate();

        $user = auth()->user();

        // Parse tags
        $tags = $this->parseTags($this->tagsInput);

        // Determine school scoping
        $schoolId = null;
        if ($this->isSchoolScoped && $user->school_id) {
            $schoolId = $user->school_id;
        }

        // Update basic fields
        $this->resource->title = $this->title;
        $this->resource->description = $this->description;
        $this->resource->tags = $tags;
        $this->resource->academic_subject_id = $this->academicSubjectId;
        $this->resource->school_id = $schoolId;

        // Handle file replacement if new file uploaded
        if ($this->file) {
            // Delete old file
            if ($this->resource->file_path && Storage::disk('public')->exists($this->resource->file_path)) {
                Storage::disk('public')->delete($this->resource->file_path);
            }

            // Determine file format based on mime type
            $mimeType = $this->file->getMimeType();
            $format = $this->determineFormat($mimeType);

            // Generate unique file name
            $originalName = $this->file->getClientOriginalName();
            $extension = $this->file->getClientOriginalExtension();
            $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'_'.time().'.'.$extension;

            // Store new file
            $path = $this->file->storeAs('educational-resources', $fileName, 'public');

            // Update file fields
            $this->resource->file_path = $path;
            $this->resource->file_name = $originalName;
            $this->resource->file_type = $mimeType;
            $this->resource->format = $format;
            $this->resource->file_size = $this->file->getSize();
        }

        $this->resource->save();

        // Sync topics and subtopics
        $this->resource->topics()->sync($this->selectedTopics);
        $this->resource->subtopics()->sync($this->selectedSubtopics);

        $this->showSuccess = true;

        // Clear the file input
        $this->reset(['file']);

        $this->dispatch('resource-updated', resourceId: $this->resource->id);
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

    protected function canEdit($user, EducationalResource $resource): bool
    {
        if (! $user) {
            return false;
        }

        // Administrators, owners, and super-admins can edit any resource
        if ($user->hasAnyRole(['admin', 'owner', 'super-admin'])) {
            return true;
        }

        // Teachers and moderators can edit their own resources
        if ($user->hasAnyRole(['teacher', 'moderator'])) {
            return $resource->uploaded_by === $user->id;
        }

        return false;
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

        return view('livewire.resources.resource-edit', [
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'academicSubjects' => $academicSubjects,
            'topics' => $topics,
            'subtopics' => $subtopics,
        ]);
    }
}
