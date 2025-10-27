<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class ExaminationSections extends Component
{

    use withFileUploads;
    public $topics;

    public $sections;

    public $subtopics;

    public $instructions;
    public $metafields = [];

    public array $selectedOptions = [];

    public function plus()
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

    public function minus()
    {
        array_pop($this->sections);
    }

    public function mount($topics)
    {
        $this->topics = $topics;

        $this->sections = old('sections', session('examination_form_data.sections', [
            ['name' => '', 'type' => '', 'count' => '', 'topics' => [], 'instructions' => '', 'subtopics' => [], 'metafields' => []],
        ]));
    }

    public function render()
    {
        return view('livewire.examination-sections');
    }

    public function countQuestions($topic, $type)
    {
        return $topic[$type . '_count'] ?? 0;
    }

    public function countSubQuestions($subtopic, $type)
    {
        return $subtopic[$type . '_count'] ?? 0;
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

    public function __construct($id = null)
    {
        $this->metafields[] = [
            'option' => null,
            'pages_count' => 1,
            'spaces_count' => 1,
            'file' => null,
        ];
    }

}
