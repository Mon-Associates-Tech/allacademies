@php
    //    $multiple = isset($sections) && is_countable($sections) && count($sections) > 1;
@endphp


<x-layouts.export>
    <div class="overflow-hidden rounded-lg bg-white shadow mx-auto print:shadow-none print:rounded-none max-w-[60rem] print:max-w-full">

        <div class="font-serif px-4 py-5 sm:p-6 print:px-0 ">

            {!!  $examination->heading->html !!}

            @foreach ($sections as $section)
                <h4 @class(["font-medium uppercase text-sm text-center", "pt-5" => !$loop->first])>{{ $section['name'] }}</h4>
                <div class="italic mb-4">
                    <div x-data="{ instructions: @js($section['instructions']) }">
                        <p x-html="marked.parse(instructions)"></p>
                    </div>
                </div>
                @if ('multiple_choice_questions' === $section['type'])
                    <ol class="list-decimal mb-12">
                        @foreach ($section['questions'] as $mc)
                            <li>
                                {{ $mc->question->html }}
                                <div x-bind:class="'elliptical' === format ? 'grid-cols-2' : 'grid-cols-1'" class="grid gap-x-5">
                                    @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
                                        @if ($mc->{"option_$o"}->up)
                                            <div class="flex space-x-2 items-baseline">
                                                <div>({{ $o }})</div>
                                                <div>{{ $mc->{"option_$o"}->html }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
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
                            </li>
                        @endforeach
                    </ol>
                @elseif ('essay_questions' === $section['type'])
                    <ol class="list-decimal mb-12">
                        @foreach ($section['questions'] as $es)
                            <li class="mb-8">
                                {{ $es->question->html }}
                                <p class="text-sm text-right --6">[{{ $es->score }} {{ Str::plural('mark', $es->score) }}]</p>
                            </li>
                        @endforeach
                    </ol>
                @endif
                {{--                {!!  $section['page'] !!}--}}
                <div class="page-break"></div>
                <div class="page-break"></div>
            @if(isset($section['document']))
                    {!! File::get(storage_path('app/public/'.$section['document'])) !!}
            @endif
            @endforeach
        </div>
    </div>
</x-layouts.export>

