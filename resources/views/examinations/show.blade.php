@php
    //    $multiple = isset($sections) && is_countable($sections) && count($sections) > 1;
@endphp


<x-print title="Examination Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('academic-subjects.examinations.index', ['academic_subject' => $examination->academicSubject]),
        ]" />
    </x-slot>

    <div x-data="{ format: 'lenticular' }"
         class="overflow-hidden rounded-lg bg-white shadow mx-auto print:shadow-none print:rounded-none max-w-[60rem] print:max-w-full">
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-end print:hidden">
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
        <div class="font-serif px-4 py-5 sm:p-6 print:px-0 ">
            <div class="text-center">
                {{ $examination->heading->html }}
            </div>

            @foreach ($sections as $section)
                <h2 @class(["font-medium uppercase text-sm text-center", "pt-5" => !$loop->first])>{{ $section['name'] }}</h2>

                @if(isset($section['instructions']))
                    <div class="italic mb-4">
                        <div x-data="{ instructions: @js($section['instructions']['down'], JSON_THROW_ON_ERROR) }">
                            <p x-html="marked.parse(instructions)"></p>
                        </div>
                    </div>
                @endif

                @if ('multiple_choice_questions' === $section['type'])
                    <ol class="list-decimal mb-12 px-4">
                        @if(isset($section['questions']) && is_countable($section['questions']))
                            @foreach ($section['questions'] as $mc)
                                <li class="py-2">
                                    <div>
                        <span class="font-medium">
                            {!! json_decode($mc['question'])->down !!}
                        </span>
                                        <div x-bind:class="'elliptical' === format ? 'grid-cols-2' : 'grid-cols-1'"
                                             class="grid gap-x-5">
                                            @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
                                                @if ($mc["option_$o"])
                                                    <div class="flex space-x-2 items-baseline">
                                                        <div>({{ $o }})</div>
                                                        <div>{!! json_decode($mc["option_$o"])->up !!}</div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ol>
                @elseif ('true_or_false_questions' === $section['type'])
                    <ol class="list-decimal mb-12 px-4">
                        @if(isset($section['questions']) && is_countable($section['questions']))
                            @foreach ($section['questions'] as $tf)
                                <li class="py-2"
                                    x-data="{ question: marked.parse(@js(json_decode($tf['question'])->down)) }">
                                    <span> {!! json_decode($tf['question'])->down !!} </span>
                                    <div x-bind:class="'elliptical' === format ? 'grid-cols-2' : 'grid-cols-1'"
                                         class="grid gap-x-5">
                                        @foreach (['a', 'b'] as $o)
                                            <div class="flex space-x-2 items-baseline">
                                                <div>({{ $o }})</div>
                                                <div>{{ 'a' === $o ? 'True' : 'False' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ol>
                @elseif ('essay_questions' === $section['type'])
                    <ol class="list-decimal mb-12 px-4">
                        @if(isset($section['questions']) && is_countable($section['questions']))
                            @foreach ($section['questions'] as $es)
                                <li class="py-2">
                                    <div>
                                    <span
                                        x-html="marked.parse(@js(json_decode($es['question'], false, 512, JSON_THROW_ON_ERROR)->down, JSON_THROW_ON_ERROR))"
                                        class="font-medium"></span>

                                        <p class="text-sm text-right">
                                            [{{ $es['score'] }} {{ Str::plural('mark', $es['score']) }}]</p>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ol>
                @endif

                @if(isset($section['page']))
                    <div class="page-break h-full">
                        <div
                            class="h-screen w-full flex items-center justify-center print:h-[100vh] print:w-full print:flex print:items-center print:justify-center">
                            <div class="text-center">
                                <h2 style="font-size: 20pt; font-weight: bold">Do not turn the next page until you are
                                    told to do so</h2>
                            </div>
                        </div>

                    </div>
                @endif

                @if (isset($section['extension']) && ( $section['extension'] === 'txt' || $section['extension'] === 'docx'))
                    <div class="whitespace-pre-wrap bg-inherit p-4 rounded text-sm">
                        {{ $section['document'] }}
                    </div>
                @elseif (isset($section['extension']) && $section['extension'] === 'pdf')
                    {{--                    <iframe src="{{ asset('storage/' . $section['original_path']) }}" width="100%" height="500px"></iframe>--}}
                    @if (!empty($section['pdf_images']))
                        @foreach ($section['pdf_images'] as $imagePath)
                            <img alt src="{{ asset('/storage/' . $imagePath) }}"
                                 class="w-full mb-4 print:break-before-page">
                        @endforeach
                    @endif
                @elseif (isset($section['extension']) && in_array($section['extension'], ['jpg', 'jpeg', 'png']))
                    <img src="{{ asset('storage/' . $section['original_path']) }}" alt="Image preview">
                @else
                    <p class="text-red-500"></p>
                @endif

            @endforeach
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-end print:hidden">
           <x-button.primary x-on:click.prevent="window.print()">Print</x-button.primary>
       </div>
    </div>

    <script>
        function getFileType(filePath) {
            const extension = filePath.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                return 'image';
            } else if (extension === 'pdf') {
                return 'pdf';
            } else if (['doc', 'docx'].includes(extension)) {
                return 'word';
            } else if (extension === 'txt') {
                return 'text';
            } else {
                return 'unknown';
            }
        }

    </script>
</x-print>
