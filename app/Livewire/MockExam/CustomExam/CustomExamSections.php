<?php

namespace App\Livewire\MockExam\CustomExam;

use Livewire\Component;
use Livewire\WithFileUploads;

class CustomExamSections extends Component
{
    use WithFileUploads;

    public array $topics;

    public array $sections;

    public array $subtopics;

    public array $instructions;

    public array $metafields = [];

    public array $selectedOptions = [];

    public function plus(): void
    {
        $this->sections[] = [
            'name' => '',
            'type' => '',
            'count' => '',
            'topics' => [],
            'instructions' => '',
            'subtopics' => [],
            'metafields' => [],
        ];
    }

    public function minus(): void
    {
        array_pop($this->sections);
    }

    public function mount($topics)
    {
        $this->topics = $topics;

        $this->sections = old('sections', [
            ['name' => '', 'type' => '', 'count' => '', 'topics' => [], 'instructions' => '', 'subtopics' => [], 'metafields' => []],
        ]);
    }

    public function render()
    {
        return view('livewire.mock-exam.custom-exam.custom-exam-sections');
    }

    public function countQuestions($topic, $type): int
    {
        return $topic[$type.'_count'] ?? 0;
    }

    public function countSubQuestions($subtopic, $type): int
    {
        return $subtopic[$type.'_count'] ?? 0;
    }

    public function addMetafield()
    {
        $this->metafields[] = [
            'option' => null,
            'pages_count' => 1,
            'spaces_count' => 1,
            'file' => null,
        ];
    }

    public function removeSection($index): void
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
    }

    public function __construct(?int $id = null)
    {
        $this->metafields[] = [
            'option' => null,
            'pages_count' => 1,
            'spaces_count' => 1,
            'file' => null,
        ];
    }
}
