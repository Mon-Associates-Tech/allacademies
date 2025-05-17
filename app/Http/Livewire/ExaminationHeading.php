<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;

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

    public function updated()
    {
        $this->compile();
    }

    public function render()
    {
        return view('livewire.examination-heading');
    }

    private function compile()
    {
        if ('twig' === $this->template) {
            $this->down = sprintf(<<<'TWIG'
            <div>
                <div class="text-center">
                    <h1 class="font-semibold uppercase">%s</h1>
                    <h1 class="font-semibold uppercase">%s &#12539; %s</h1>
                    <h1 class="font-semibold">Duration: %s</h1>
                </div>
                <div class="border-y border-black my-5 py-5">
                    <h1 class="font-semibold uppercase">Instructions:</h1>
                    <p class="font-semibold">%s</p>
                </div>
            </div>
            TWIG, $this->title ?: 'TITLE', $this->metadata['level_label'], $this->metadata['subject_code'], $this->duration ? convertMinutesToHoursMinutes($this->duration) : 'DURATION', $this->instructions ?: 'INSTRUCTIONS');
        }

        if ('pug' === $this->template) {
            $this->down = sprintf(<<<'PUG'
            <div class="border border-black p-5 mb-5 space-y-5">
                <div class="text-center">
                    <h1 class="font-semibold uppercase">%s</h1>
                </div>
                <div class="text-center">
                    <h1 class="font-semibold uppercase">%s</h1>
                    <h1 class="font-semibold uppercase">%s</h1>
                </div>
                <div class="text-center">
                    <h1 class="font-semibold uppercase">Time Allowed: %s</h1>
                </div>
                <div>
                    <h1 class="font-semibold uppercase">Instructions:</h1>
                    <p class="font-semibold">%s</p>
                </div>
            </div>
            PUG, $this->title ?: 'TITLE', $this->metadata['level_name'], $this->metadata['subject_name'], $this->duration ? convertMinutesToHoursMinutes($this->duration) : 'DURATION', $this->instructions ?: 'INSTRUCTIONS');
        }

        if ('tera' === $this->template) {
            $this->down = sprintf(<<<'TERA'
            <div class="space-y-5 mb-5">
                <div class="text-center">
                    <img src="%s" alt="logo" class="w-20 mx-auto">
                    %s
                </div>
                <div>
                    <h1 class="font-semibold uppercase">%s</h1>
                    <h1 class="font-semibold uppercase">%s %s</h1>
                </div>
                <div>
                    <h1 class="font-semibold uppercase">INSTRUCTIONS:</h1>
                    <p class="font-semibold uppercase">%s</p>
                </div>
                <div>
                    <h1 class="font-semibold uppercase">Time Allowed: %s</h1>
                </div>
            </div>
            TERA, $this->metadata['logo'], $this->generate('<h1 class="font-semibold uppercase"></h1>'), $this->title ?: 'TITLE', $this->metadata['subject_code'], $this->metadata['subject_name'], $this->instructions ?: 'INSTRUCTIONS', $this->duration ? convertMinutesToHoursMinutes($this->duration) : 'DURATION');
        }

        if ('jinja' === $this->template) {
            $this->down = sprintf(<<<'JINJA'
            <div class="space-y-5 mb-5">
                <div class="flex items-center space-x-5">
                    <div class="flex-none border-4 border-black p-2 max-w-xs">
                        <h1 class="font-semibold uppercase text-sm">%s</h1>
                        <h1 class="font-semibold uppercase text-sm">%s</h1>
                        <h1 class="font-semibold uppercase text-sm">%s</h1>
                        <h1 class="font-semibold text-sm">%s</h1>
                    </div>
                    <div class="space-y-5 overflow-hidden">
                        <p class="flex items-center space-x-2 font-semibold"><span class="flex-none">Name:</span><span class="">%s</span></p>
                        <p class="flex items-center space-x-2 font-semibold"><span class="flex-none">Index Number:</span><span class="">%s</span></p>
                    </div>
                </div>
                <div class="text-center">
                    %s
                </div>
                <div class="flex items-center justify-between">
                    <span>%s</span>
                    <span class="uppercase">%s</span>
                    <span>%s</span>
                </div>
                <p>Instructions: %s</p>
            </div>
            JINJA, $this->metadata['subject_code'], $this->title ?: 'TITLE', $this->metadata['subject_name'], $this->duration ? convertMinutesToHoursMinutes($this->duration) : 'DURATION', Str::repeat('.', 500), Str::repeat('.', 500), $this->generate('<h1 class="font-semibold uppercase"></h1>'), $this->metadata['level_label'], $this->metadata['subject_name'], $this->duration ? convertMinutesToHoursMinutes($this->duration) : 'DURATION', $this->instructions ?: 'INSTRUCTIONS');
        }
    }

    private function generate(string $template): string
    {
        $template = Str::of($template);
//        $details = [$this->metadata['institution']];

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

        $details = array_map(fn ($detail) => $template->replace('><', ">{$detail}<"), $details);

        return implode(PHP_EOL, $details);
    }
}
