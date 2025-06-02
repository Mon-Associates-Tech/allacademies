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

    public function mount($metadata)
    {
        $this->metadata = $metadata;
        $this->template = old('heading.template', 'twig');
        $this->title = old('heading.title', '');
        $this->duration = old('heading.duration', '');
        $this->instructions = old('heading.instructions', '');

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

    public function updated()
    {
        $this->compile();
    }

    public function render()
    {
        return view('livewire.examination-heading');
    }
}
