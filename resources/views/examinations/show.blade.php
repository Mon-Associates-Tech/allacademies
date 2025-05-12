@php
    $multiple = count($sections) > 1;
@endphp

<x-auth title="Examination Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('academic-subjects.examinations.index', ['academic_subject' => $examination->academicSubject]),
        ]" />
    </x-slot>

    <div x-data="{ format: 'lenticular' }" class="overflow-hidden rounded-lg bg-white shadow print:shadow-none print:rounded-none">
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-end print:hidden">
            <span class="inline-flex rounded-md shadow-sm">
                <span class="inline-flex items-center rounded-l-md border border-gray-300 bg-white px-2 py-1 sm:text-sm sm:leading-6">
                    Format
                </span>
                <select x-model="format" id="format" name="format" class="-ml-px block w-full rounded-l-none rounded-r-md border-0 bg-white py-1 pl-3 pr-9 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6">
                    <option value="lenticular">Lenticular</option>
                    <option value="elliptical">Elliptical</option>
                </select>
            </span>
        </div>
        <div class="font-serif px-4 py-5 sm:p-6 print:px-0">
            {{ $examination->heading->html }}

            @foreach ($sections as $section)
                <h2 @class(["font-medium uppercase text-sm text-center", "pt-5" => !$loop->first])>{{ $section['name'] }}</h2>
                    <div class="text-center italic mb-4">
                        {{$section['instructions']}}
                    </div>
                @if ('multiple_choice_questions' === $section['type'])
                <ol class="list-decimal mb-12">
                    @foreach ($section['questions'] as $mc)
                        <li>
                            {{ $mc->question->html }}
                            <div x-bind:class="'elliptical' === format ? 'grid-cols-2' : 'grid-cols-1'" class="grid gap-x-5">
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
                                @if ($mc->{"option_{$o}"}->up)
                                    <div class="flex space-x-2 items-baseline">
                                        <div>({{ $o }})</div>
                                        <div>{{ $mc->{"option_{$o}"}->html }}</div>
                                    </div>
                                @endif
                            @endforeach
                            </div>
{{--                            <p class="text-sm text-right">[{{ $mc->score }} {{ Str::plural('mark', $mc->score) }}]</p>--}}
                        </li>
                    @endforeach
                </ol>
                @elseif ('true_or_false_questions' === $section['type'])
                <ol class="list-decimal mb-12">
                    @foreach ($section['questions'] as $tf)
                        <li>
                            {{ $tf->question->html }}
                            <div x-bind:class="'elliptical' === format ? 'grid-cols-2' : 'grid-cols-1'" class="grid gap-x-5">
                            @foreach (['a', 'b'] as $o)
                                <div class="flex space-x-2 items-baseline">
                                    <div>({{ $o }})</div>
                                    <div>{{ 'a' === $o ? 'True' : 'False' }}</div>
                                </div>
                            @endforeach
                            </div>
{{--                            <p class="text-sm text-right">[{{ $tf->score }} {{ Str::plural('mark', $tf->score) }}]</p>--}}
                        </li>
                    @endforeach
                </ol>
                @elseif ('essay_questions' === $section['type'])
                <ol class="list-decimal mb-12">
                    @foreach ($section['questions'] as $es)
                        <li>
                            {{ $es->question->html }}
                            <p class="text-sm text-right">[{{ $es->score }} {{ Str::plural('mark', $es->score) }}]</p>
                        </li>
                    @endforeach
                </ol>
                @endif
            @endforeach
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-end print:hidden">
            <x-button.primary x-on:click="window.print()">Print</x-button.primary>
        </div>
    </div>
</x-auth>
