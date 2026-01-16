<?php

namespace App\Livewire;

use App\Templates\TemplateRenderer;
use Illuminate\Support\Str;
use Livewire\Component;

class ExaminationHeading extends Component
{
    public $down;

    public $up;

    public $template;

    public $title;

    public $duration;

    public $instructions;

    public $metadata;

    protected $rules = [
        'title' => 'required|string|min:1',
        'duration' => 'required|string|min:1',
        'template' => 'required|in:twig,pug,tera,jinja',
        'instructions' => 'nullable|string',
    ];

    protected $messages = [
        'title.required' => 'The title field is required.',
        'duration.required' => 'The duration field is required.',
        'template.required' => 'The template field is required.',
        'template.in' => 'The selected template is invalid.',
    ];

    public function mount($metadata)
    {
        $this->metadata = $metadata;
        $this->template = old('heading.template', 'twig');
        $this->title = old('heading.title', '');
        $this->duration = old('heading.duration', '');

        $this->compile();
    }

    private function compile()
    {
        if ($this->template === 'twig') {
            $this->down = TemplateRenderer::renderTwig($this->instructions, $this->duration, $this->title, $this->metadata);
        }

        if ($this->template === 'pug') {
            $this->down = TemplateRenderer::renderPug($this->instructions, $this->duration, $this->title, $this->metadata);

        }

        if ($this->template === 'tera') {
            $this->down = TemplateRenderer::renderTera($this->instructions, $this->duration, $this->title, $this->metadata, $this->generate($this->template));

        }

        if ($this->template === 'jinja') {
            $this->down = TemplateRenderer::renderJinja($this->instructions, $this->duration, $this->title, $this->metadata, $this->generate($this->template));
        }
    }

    private function generate(string $template): string
    {
        $template = Str::of($template);
        $details = [$this->metadata['institution']];

        if ($this->metadata['type'] === 'college') {
            $details[] = $this->metadata['college'];
            $details[] = $this->metadata['school'];
        }

        if ($this->metadata['type'] === 'faculty') {
            $details[] = $this->metadata['faculty'];
        }

        if ($this->metadata['type'] !== 'institution') {
            $details[] = $this->metadata['department'];
        }

        $details = array_map(fn ($detail) => $template->replace('><', ">{$detail}<"), $details);

        return implode(PHP_EOL, $details);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->compile();
    }

    public function render()
    {
        return view('livewire.examination-heading');
    }
}
