<?php

namespace App\Livewire;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExaminationSections extends Component
{

    use withFileUploads;
    public array $topics;

    public array $sections;

    public array $subtopics;

    public array $instructions;
    public array $metafields = [];

    public array $selectedOptions = [];

    public function plus():void
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

    public function minus():void
    {
        array_pop($this->sections);
    }

    public function mount($topics)
    {
        $this->topics = $topics;

        $this->sections = old('sections',  [
            ['name' => '', 'type' => '', 'count' => '', 'topics' => [], 'instructions' => '', 'subtopics' => [], 'metafields' => []],
        ]);
    }

    public function render()
    {
        return view('livewire.examination-sections');
    }

    public function countQuestions(AcademicTopic $topic, mixed $type):int
    {
        return $topic[$type . '_count'] ?? 0;
    }

    public function countSubQuestions(AcademicSubtopic $subtopic, mixed $type):int
    {
        return $subtopic[$type . '_count'] ?? 0;
    }

    public function addMetafield(): array
    {
        $this->metafields[] = [
            'option' => null,
            'pages_count' => 1,
            'spaces_count' => 1,
            'file' => null,
        ];
    }

    public function __construct(int $id = null)
    {
        $this->metafields[] = [
            'option' => null,
            'pages_count' => 1,
            'spaces_count' => 1,
            'file' => null,
        ];
    }

}
