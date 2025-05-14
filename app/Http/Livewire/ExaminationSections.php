<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ExaminationSections extends Component
{
    public $topics;

    public $sections;

    public $subtopics;

    public $instructions;

    public function plus()
    {
        $this->sections[] = [
            'name' => '',
            'type' => '',
            'count' => 2,
            'topics' => [],
            'instructions' => '',
        ];
    }

    public function minus()
    {
        array_pop($this->sections);
    }

    public function mount($topics)
    {
        $this->topics = $topics;

        $this->sections = old('sections') ?? [
            ['name' => '', 'type' => '', 'count' => '', 'topics' => [], 'instructions' => ''],
        ];
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

}
