<?php

namespace App\Livewire\ExaminationHub;

use Livewire\Component;

class AcademicClassification extends Component
{
    public ?int $academicGroupId = null;
    public ?int $academicLevelId = null;
    public ?int $academicSubjectId = null;
    public array $hierarchyTree = [];

    protected $listeners = ['selection-changed' => 'handleSelectionChanged'];

    public function mount(?int $academicGroupId = null, ?int $academicLevelId = null, ?int $academicSubjectId = null, array $hierarchyTree = []): void
    {
        $this->academicGroupId = $academicGroupId;
        $this->academicLevelId = $academicLevelId;
        $this->academicSubjectId = $academicSubjectId;
        $this->hierarchyTree = $hierarchyTree;
    }

    public function handleSelectionChanged($event): void
    {
        $payload = is_array($event) && isset($event[0]) ? $event[0] : $event;
        if (!is_array($payload) || empty($payload['name'])) {
            return;
        }

        $name = (string) $payload['name'];
        $selected = $payload['selected'] ?? [];
        $value = is_array($selected) ? (count($selected) > 0 ? (int) reset($selected) : null) : (int) $selected;

        if ($name === 'academic_group_id') {
            $this->academicGroupId = $value;
            $this->academicLevelId = null;
            $this->academicSubjectId = null;
        } elseif ($name === 'academic_level_id') {
            $this->academicLevelId = $value;
            $this->academicSubjectId = null;
        } elseif ($name === 'academic_subject_id') {
            $this->academicSubjectId = $value;
        }

        $this->dispatch('exam-hierarchy-changed',
            academicGroupId: $this->academicGroupId,
            academicLevelId: $this->academicLevelId,
            academicSubjectId: $this->academicSubjectId,
        );

        $this->dispatch('$refresh');
    }

    public function levelItems(): array
    {
        if (!$this->academicGroupId) {
            return [];
        }

        $group = collect($this->hierarchyTree)->firstWhere('id', $this->academicGroupId);
        return array_map(fn($level) => ['id' => $level['id'], 'name' => $level['name']], $group['levels'] ?? []);
    }

    public function subjectItems(): array
    {
        if (!$this->academicLevelId) {
            return [];
        }

        foreach ($this->hierarchyTree as $group) {
            foreach (($group['levels'] ?? []) as $level) {
                if ((int) $level['id'] === $this->academicLevelId) {
                    return array_map(fn($subject) => ['id' => $subject['id'], 'name' => $subject['name']], $level['subjects'] ?? []);
                }
            }
        }

        return [];
    }

    public function render()
    {
        return view('livewire.examination-hub.academic-classification');
    }
}
