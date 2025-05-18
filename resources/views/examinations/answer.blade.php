@php
    $multiple = count($sections) > 1;
@endphp

<x-auth title="Examination Details" :has-action="false">
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
            {{ Str::of($examination->heading->html)->replace($examination->title, "{$examination->title} Answers")->toHtmlString() }}

            @foreach ($sections as $section)
                @if ($multiple)
                <h2 @class(["font-medium uppercase text-sm", "pt-5" => !$loop->first])>{{ $section['name'] }}</h2>
                @endif

                @if ('multiple_choice_questions' === $section['type'])
                <ol class="list-decimal">
                    @foreach ($section['questions'] as $mc)
                        <li class="uppercase">{{ $mc->answer }}</li>
                    @endforeach
                </ol>
                @elseif ('true_or_false_questions' === $section['type'])
                <ol class="list-decimal">
                    @foreach ($section['questions'] as $tf)
                        <li>{{ $tf->answer ? 'True' : 'False' }}</li>
                    @endforeach
                </ol>
                @elseif ('essay_questions' === $section['type'])
                <ol class="list-decimal">
                    @foreach ($section['questions'] as $es)
                        <li>
                            {{ $es->answer->html }}
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
