<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ExaminationSections extends Component
{
    public $topics;

    public $sections;

    public function plus()
    {
        array_push($this->sections, [
            'name' => '',
            'type' => '',
            'count' => '',
            'topics' => []
        ]);
    }

    public function minus()
    {
        array_pop($this->sections);
    }

    public function mount($topics)
    {
        $this->topics = $topics;

        $this->sections = old('sections') ?? [
            ['name' => '', 'type' => '', 'count' => '', 'topics' => []]
        ];
    }

    public function render()
    {
        return view('livewire.examination-sections');
    }
}
