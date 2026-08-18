<?php

namespace App\Livewire\ExaminationHub;

use Livewire\Component;
use Livewire\WithFileUploads;

class SectionBuilder extends Component
{
    use WithFileUploads;

    public array $sections = [];

    public array $hierarchyTree = [];

    public array $uploadedDocuments = [];

    public ?int $examAcademicGroupId   = null;
    public ?int $examAcademicLevelId   = null;
    public ?int $examAcademicSubjectId = null;

    protected $listeners = [
        'selection-changed'    => 'handleSelectionChanged',
        'exam-hierarchy-changed' => 'syncExamHierarchy',
    ];

    public function mount(array $sections = [], array $hierarchyTree = []): void
    {
        $this->sections = ! empty($sections) ? array_values($sections) : [$this->blankSection()];
        $this->hierarchyTree = $hierarchyTree;

        // Seed exam-level hierarchy from the first section (used when editing)
        if (!empty($this->sections[0]['academic_subject_id'])) {
            $this->examAcademicGroupId   = $this->sections[0]['academic_group_id'] ?? null;
            $this->examAcademicLevelId   = $this->sections[0]['academic_level_id'] ?? null;
            $this->examAcademicSubjectId = $this->sections[0]['academic_subject_id'] ?? null;
        }
    }

    public function updatedUploadedDocuments($value, $key): void
    {
        if (preg_match('/^(\d+)$/', $key, $matches)) {
            $index = (int) $matches[1];
            if (isset($this->sections[$index]) && $value) {
                try {
                    // Get the file - $value is the uploaded file object
                    $file = is_array($value) ? ($value[0] ?? null) : $value;
                    
                    if (!$file || !is_object($file) || !method_exists($file, 'getRealPath')) {
                        return;
                    }
                    
                    // Get file info
                    $tempPath = $file->getRealPath();
                    $fileName = $file->getClientOriginalName();
                    
                    // Copy to permanent storage
                    $storagePath = 'temp-exam-documents/' . uniqid() . '_' . $fileName;
                    \Illuminate\Support\Facades\Storage::disk('local')->put(
                        $storagePath,
                        file_get_contents($tempPath)
                    );
                    
                    $permanentPath = storage_path('app/' . $storagePath);
                    
                    $this->sections[$index]['has_document'] = true;
                    $this->sections[$index]['document_path'] = $permanentPath;
                    $this->sections[$index]['document_name'] = $fileName;
                    
                    \Illuminate\Support\Facades\Log::info('Document uploaded for section', [
                        'index' => $index,
                        'name' => $fileName,
                        'path' => $permanentPath,
                        'exists' => file_exists($permanentPath),
                        'dispatching_event' => 'document-uploaded-' . $index,
                    ]);
                    
                    // Dispatch browser event to update Alpine.js state
                    $this->dispatch('document-uploaded-' . $index, 
                        path: $permanentPath,
                        name: $fileName
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to store uploaded document', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'index' => $index,
                    ]);
                }
            }
        }
    }

    public function syncExamHierarchy(int|null $academicGroupId, int|null $academicLevelId, int|null $academicSubjectId): void
    {
        $this->examAcademicGroupId   = $academicGroupId;
        $this->examAcademicLevelId   = $academicLevelId;
        $this->examAcademicSubjectId = $academicSubjectId;

        foreach ($this->sections as $i => $section) {
            $this->sections[$i]['academic_group_id']   = $academicGroupId;
            $this->sections[$i]['academic_level_id']   = $academicLevelId;
            $this->sections[$i]['academic_subject_id'] = $academicSubjectId;
            $this->sections[$i]['topic_ids']           = [];
            $this->sections[$i]['subtopic_ids']        = [];
        }
        $this->dispatch('$refresh');
    }

    public function addSection(): void
    {
        $blank = $this->blankSection();
        $blank['academic_group_id']   = $this->examAcademicGroupId;
        $blank['academic_level_id']   = $this->examAcademicLevelId;
        $blank['academic_subject_id'] = $this->examAcademicSubjectId;
        $this->sections[] = $blank;
    }

    public function removeSection(int $index): void
    {
        if (count($this->sections) <= 1) {
            return;
        }
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
    }

    public function handleSelectionChanged($event): void
    {
        $payload = is_array($event) && isset($event[0]) ? $event[0] : $event;
        if (! is_array($payload) || empty($payload['name'])) {
            return;
        }

        $name = (string) $payload['name'];
        $selected = $payload['selected'] ?? [];

        if (! preg_match('/^sections\[(\d+)\]\[(.+)\]$/', $name, $matches)) {
            return;
        }

        $index = (int) $matches[1];
        $field = $matches[2];
        if (! isset($this->sections[$index])) {
            return;
        }

        if (in_array($field, ['topic_ids', 'subtopic_ids'], true)) {
            $this->sections[$index][$field] = array_values(array_map('intval', (array) $selected));
            $this->dispatch('$refresh');
            return;
        }

        $value = is_array($selected) ? (count($selected) > 0 ? (int) reset($selected) : null) : (int) $selected;
        $this->sections[$index][$field] = $value;

        if ($field === 'academic_group_id') {
            $this->sections[$index]['academic_level_id'] = null;
            $this->sections[$index]['academic_subject_id'] = null;
            $this->sections[$index]['topic_ids'] = [];
            $this->sections[$index]['subtopic_ids'] = [];
        } elseif ($field === 'academic_level_id') {
            $this->sections[$index]['academic_subject_id'] = null;
            $this->sections[$index]['topic_ids'] = [];
            $this->sections[$index]['subtopic_ids'] = [];
        } elseif ($field === 'academic_subject_id') {
            $this->sections[$index]['topic_ids'] = [];
            $this->sections[$index]['subtopic_ids'] = [];
        }

        $this->dispatch('$refresh');
    }

    public function levelItems(int $index): array
    {
        $groupId = (int) ($this->sections[$index]['academic_group_id'] ?? 0);
        $group = collect($this->hierarchyTree)->firstWhere('id', $groupId);

        return array_map(fn ($level) => ['id' => $level['id'], 'name' => $level['name']], $group['levels'] ?? []);
    }

    public function subjectItems(int $index): array
    {
        $levelId = (int) ($this->sections[$index]['academic_level_id'] ?? 0);
        $levels = $this->levelItems($index);

        foreach ($this->hierarchyTree as $group) {
            foreach (($group['levels'] ?? []) as $level) {
                if ((int) $level['id'] === $levelId) {
                    return array_map(fn ($subject) => ['id' => $subject['id'], 'name' => $subject['name']], $level['subjects'] ?? []);
                }
            }
        }

        return [];
    }

    public function topicItems(int $index): array
    {
        $subjectId = (int) ($this->sections[$index]['academic_subject_id'] ?? 0);
        foreach ($this->hierarchyTree as $group) {
            foreach (($group['levels'] ?? []) as $level) {
                foreach (($level['subjects'] ?? []) as $subject) {
                    if ((int) $subject['id'] === $subjectId) {
                        return array_map(fn ($topic) => ['id' => $topic['id'], 'name' => $topic['name']], $subject['topics'] ?? []);
                    }
                }
            }
        }

        return [];
    }

    public function subtopicItems(int $index): array
    {
        $selectedTopics = array_map('intval', $this->sections[$index]['topic_ids'] ?? []);
        $subjectId = (int) ($this->sections[$index]['academic_subject_id'] ?? 0);
        $items = [];

        foreach ($this->hierarchyTree as $group) {
            foreach (($group['levels'] ?? []) as $level) {
                foreach (($level['subjects'] ?? []) as $subject) {
                    if ((int) $subject['id'] !== $subjectId) {
                        continue;
                    }
                    foreach (($subject['topics'] ?? []) as $topic) {
                        if (! in_array((int) $topic['id'], $selectedTopics, true)) {
                            continue;
                        }
                        foreach (($topic['subtopics'] ?? []) as $subtopic) {
                            $items[] = ['id' => $subtopic['id'], 'name' => $subtopic['name']];
                        }
                    }
                }
            }
        }

        return $items;
    }

    private function blankSection(): array
    {
        return [
            'title' => '',
            'description' => '',
            'instructions' => '',
            'time_limit_minutes' => '',
            'source_type' => 'database',
            'question_type' => 'multiple_choice',
            'question_count' => 10,
            'database_count' => 0,
            'ai_count' => 0,
            'manual_count' => 0,
            'is_randomized' => false,
            'academic_group_id' => null,
            'academic_level_id' => null,
            'academic_subject_id' => null,
            'topic_ids' => [],
            'subtopic_ids' => [],
            'has_document' => false,
        ];
    }

    public function render()
    {
        return view('livewire.examination-hub.section-builder');
    }
}

