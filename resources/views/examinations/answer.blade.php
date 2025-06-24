@php
    $multiple = count($sections) > 1;
@endphp

<x-print title="Examination Details" :has-action="false">

    <div x-data="{ format: 'lenticular' }"
         class="overflow-hidden rounded-lg bg-white shadow print:shadow-none print:rounded-none max-w-4xl mx-auto">
        <div class="text-center py-3 text-xl font-bold uppercase">{{$examination->title}}</div>

        <div class="bg-gray-50 px-4 py-4 border-b border-t border-gray-200 sm:px-6 flex items-center justify-between print:hidden">
            <div>
                <x-link.white :to="route('examinations.index', ['academic_subject' => $examination->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Back to Exams
                </x-link.white>
            </div>

            <span class="inline-flex rounded-md shadow-sm">
                <span
                    class="inline-flex items-center rounded-l-md border border-gray-300 bg-white px-2 py-1 sm:text-sm sm:leading-6">
                    Format
                </span>
                <select x-model="format" id="format" name="format"
                        class="-ml-px block w-full rounded-l-none rounded-r-md border-0 bg-white py-1 pl-3 pr-9 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6">
                    <option value="lenticular">Lenticular</option>
                    <option value="elliptical">Elliptical</option>
                </select>
            </span>
        </div>
        <div class="font-serif px-4 py-5 sm:p-6 print:px-0">
            @if(isset($examination->metadata))
                <div>
                    <div class="text-center font-bold uppercase">
                        {{$examination->title . ' Answers'}}
                    </div>

                    <div class="text-center font-bold uppercase">
                        {{ $examination->academicSubject->name }}
                    </div>
                    <div class="text-center font-bold uppercase">
                        {!! $examination->metadata['level_label'] .'&#12539;'. $examination->metadata['subject_code'] ?? $examination->academicSubject->code !!}
                    </div>

                    <div class="text-center font-bold uppercase">
                        {{ $examination->created_at->format('F Y') }}
                    </div>
                </div>
            @else
                {{ Str::of($examination->heading->html)->replace($examination->title, "{$examination->title} Answers")->toHtmlString() }}
            @endif

            @foreach ($sections as $section)
                @if ($multiple)
                    <h2 @class(["font-medium uppercase text-sm", "pt-5" => !$loop->first])>{{ $section['name'] }}</h2>
                @endif

                @if ('multiple_choice_questions' === $section['type'])
                    <ol class="list-decimal px-4">
                        @foreach ($section['questions'] as $mc)
                            <li class="uppercase">{{ $mc->answer }}</li>
                        @endforeach
                    </ol>
                @elseif ('true_or_false_questions' === $section['type'])
                    <ol class="list-decimal px-4">
                        @foreach ($section['questions'] as $tf)
                            <li>{{ $tf->answer ? 'True' : 'False' }}</li>
                        @endforeach
                    </ol>
                @elseif ('essay_questions' === $section['type'])
                    <ol class="list-decimal px-4">
                        @foreach ($section['questions'] as $es)
                            <li x-html="marked.parse(@js($es->answer->up, JSON_THROW_ON_ERROR))">
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
</x-print>
