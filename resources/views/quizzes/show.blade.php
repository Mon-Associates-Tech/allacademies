<x-layouts.app title="Quiz Review" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]"/>
    </x-slot>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <dl class="divide-y divide-gray-100">
            @foreach ($sections as $section)
                @foreach ($section['questions'] as $question)
                    <div class="px-4 py-6 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-900 sm:col-span-2">
                            {!! $question->question->up !!}
                            <p class="text-sm mt-1">[{{$question->score}} {{ Str::plural('mark', $question->score) }}
                                ]</p>
                        </dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-3 sm:mt-0">
                            @if ($question instanceof \App\Models\MultipleChoiceQuestion)
                                @foreach (['a', 'b', 'c', 'd', 'e'] as $option)
                                    <div class="flex space-x-2">
                        <span class="w-8 flex-none">
                            @if (isset($section['sheets'][$question->id]) && $section['sheets'][$question->id] === $question->answer && $question->answer === $option)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif (isset($section['sheets'][$question->id]) && $section['sheets'][$question->id] === $option)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @elseif ($question->answer === $option)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            @endif
                        </span>
                                        <span class="uppercase">{{ $option }})</span>
                                        <span>{!! $question->{"option_{$option}"}->up !!}</span>
                                    </div>
                                @endforeach
                            @endif

                            @if ($question instanceof \App\Models\TrueOrFalseQuestion)
                                @foreach ([true, false] as $option)
                                    <div class="flex space-x-2">
                        <span class="w-8 flex-none">
                            @if (isset($section['sheets'][$question->id]) && $section['sheets'][$question->id] === $question->answer && $question->answer === $option)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif (isset($section['sheets'][$question->id]) && $section['sheets'][$question->id] === $option)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @elseif ($question->answer === $option)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            @endif
                        </span>
                                        <span>{{ $option ? 'True' : 'False' }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </dd>
                    </div>
                @endforeach
            @endforeach
        </dl>
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-end space-x-5">
            <span class="font-bold">{{ $score['value'] }} / {{ $score['max'] }}</span>
            <x-link.primary
                :to="route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                Back to Quizzes
            </x-link.primary>
        </div>
    </div>
</x-layouts.app>
