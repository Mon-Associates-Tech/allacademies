<?php

namespace App\Templates;

use Closure;
use Illuminate\Support\Str;

class TemplateRenderer
{

    public function render($template = 'twig')
    {

    }

    public static function renderTwig($instructions, $duration, $title, $metadata): string
    {

            // Validate metadata array keys
            $levelLabel = $metadata['level_label'] ?? 'N/A';
            $subjectCode = $metadata['subject_code'] ?? 'N/A';

            // Escape HTML content
            $title = htmlspecialchars($title ?: 'TITLE');
            $instructions = $instructions ?? 'INSTRUCTIONS';

            // Validate duration before conversion
            $duration = $duration && is_numeric($duration)
                ? convertMinutesToHoursMinutes($duration)
                : 'DURATION';

            return sprintf(<<<'TWIG'
    <div>
        <div class="text-center">
            <h1 class="font-semibold uppercase">%s</h1>
            <h1 class="font-semibold uppercase">%s &#12539; %s</h1>
            <h1 class="font-semibold">Duration: %s</h1>
        </div>
        <div class="border-y border-black my-5 py-5">
            <h1 class="font-semibold uppercase text-center">Instructions:</h1>
            <p class="font-semibold text-start" style="text-align:start">%s</p>
        </div>
    </div>
    TWIG, $title, $levelLabel, $subjectCode, $duration, $instructions);
        }

        public static function renderPug($instructions, $duration, $title, $metadata): string
        {
            return   sprintf(<<<'PUG'
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
            PUG, $title ?: 'TITLE', $metadata['level_name'], $metadata['subject_name'], $duration ? convertMinutesToHoursMinutes($duration) : 'DURATION', $instructions ?: 'INSTRUCTIONS');
        }

        public function renderTera($instructions, $duration, $title, $metadata,  $generate): string
        {

                return  sprintf(<<<'TERA'
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
            TERA, $metadata['logo'], $generate('<h1 class="font-semibold uppercase"></h1>'), $title ?: 'TITLE', $metadata['subject_code'], $metadata['subject_name'], $instructions ?: 'INSTRUCTIONS', $duration ? convertMinutesToHoursMinutes($duration) : 'DURATION');

        }

        public function renderJinja($instructions, $duration, $title, $metadata, $generate): string{

                return sprintf(<<<'JINJA'
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
            JINJA, $metadata['subject_code'], $title ?: 'TITLE', $metadata['subject_name'], $duration ? convertMinutesToHoursMinutes($duration) : 'DURATION', Str::repeat('.', 500), Str::repeat('.', 500), $generate('<h1 class="font-semibold uppercase"></h1>'), $metadata['level_label'], $metadata['subject_name'], $duration ? convertMinutesToHoursMinutes($duration) : 'DURATION', $instructions ?: 'INSTRUCTIONS');

        }

}
