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
        $this->template = old('heading.template',  'twig');
        $this->title = old('heading.title', '');
        $this->duration = old('heading.duration', '');



        $this->compile();
    }

    private function compile()
    {
        if ('twig' === $this->template) {
            $this->down =   TemplateRenderer::renderTwig($this->instructions, $this->duration, $this->title, $this->metadata);
        }

        if ('pug' === $this->template) {
            $this->down = TemplateRenderer::renderPug($this->instructions, $this->duration, $this->title, $this->metadata);

        }

        if ('tera' === $this->template) {
            $this->down = TemplateRenderer::renderTera($this->instructions, $this->duration, $this->title, $this->metadata, $this->generate($this->template));

        }

        if ('jinja' === $this->template) {
            $this->down = TemplateRenderer::renderJinja($this->instructions, $this->duration, $this->title, $this->metadata, $this->generate($this->template));
        }
    }

    private function generate(string $template): string
    {
        $template = Str::of($template);
        $details = [$this->metadata['institution']];

        if ('college' === $this->metadata['type']) {
            $details[] = $this->metadata['college'];
            $details[] = $this->metadata['school'];
        }

        if ('faculty' === $this->metadata['type']) {
            $details[] = $this->metadata['faculty'];
        }

        if ('institution' !== $this->metadata['type']) {
            $details[] = $this->metadata['department'];
        }

        $details = array_map(fn($detail) => $template->replace('><', ">{$detail}<"), $details);

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
