@php
    $options = [
        'multiple_choice_questions' => 'Multiple Choice',
        'true_or_false_questions' => 'True/False',
        'essay_questions' => 'Essay'
    ];

    $count = function ($topic, $type) {
        return $topic[$type . '_count'] ?? 0;
    };

    $subcount = function ($subtopic, $type) {
        return $subtopic[$type . '_count'] ?? 0;
    };
@endphp

<div class="mt-5">
    <label class="block text-gray-800 font-bold">Sections</label>

    @foreach ($sections as $sectionIndex => $section)

        <div class="mt-3 bg-white border-x border-t border-gray-300 rounded-t-lg p-5 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-x-3">
                <div class="col-span-2">
                    <x-form.input wire:key="name-{{ $sectionIndex }}" wire:model="sections.{{ $sectionIndex }}.name"
                                  name="sections[{{ $sectionIndex }}][name]" type="text" label="Name"/>
                </div>
                <div>
                    <x-form.select wire:key="type-{{ $sectionIndex }}" wire:model="sections.{{ $sectionIndex }}.type"
                                   name="sections[{{ $sectionIndex }}][type]" type="text" label="Type"
                                   :options="$options"/>
                </div>
                <div>
                    <x-form.input wire:key="count-{{ $sectionIndex }}" wire:model="sections.{{ $sectionIndex }}.count"
                                  name="sections[{{ $sectionIndex }}][count]" type="number" label="Number of Questions"/>
                </div>
            </div>

            <div class="my-5">
                <x-form.rich-editor class="rich-editor" wire:key="instructions-{{ $sectionIndex }}"
                                    wire:model="sections.{{ $sectionIndex }}.instructions"
                                    name="sections[{{ $sectionIndex }}][instructions]" type="text" label="Instructions"/>
            </div>

            <label class="block text-gray-800 font-medium text-sm mt-3">Available Questions</label>

            <fieldset>
                <div class="overflow-hidden rounded-lg border border-gray-300 bg-white mt-3">
                    <ul role="list" class="divide-y divide-gray-300">
                        @foreach ($topics as $topicIndex => $topic)
                            <li class="p-3" x-data="{ showSubtopics: false }">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input
                                                wire:key="topic-{{ $sectionIndex }}-{{ $topic['id'] }}"
                                                wire:model="sections.{{ $sectionIndex }}.topics"
                                                name="sections[{{ $sectionIndex }}][topics][]"
                                                value="{{ $topic['id'] }}"
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                @change="showSubtopics = $event.target.checked"
                                            >
                                        </div>
                                        <div class="ml-3 leading-6">
                                            <label class="font-medium text-gray-700">{{ $topic['name'] }}</label>
                                        </div>
                                    </div>

                                    <div>
                                        @can('administrate')
                                            <span class="text-gray-500 mr-2">{{ $topic['questions_count'] }} Available</span>
                                        @endcan

                                        <span class="inline-flex items-center rounded-full px-1.5 py-1.5 {{ $count($topic, $section['type']) > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            <svg class="h-1.5 w-1.5 {{ $count($topic, $section['type']) > 0 ? 'fill-green-500' : 'fill-red-500' }}" viewBox="0 0 6 6" aria-hidden="true">
                                                <circle cx="3" cy="3" r="3" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div x-show="showSubtopics"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     style="display: none;"
                                     class="mt-2 space-y-2 ps-5"
                                >
                                    @foreach ($topic['subtopics'] as $subIndex => $subtopic)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="relative flex items-start">
                                                <div class="flex h-6 items-center">
                                                    <input
                                                        wire:key="subtopic-{{ $sectionIndex }}-{{ $subtopic['id'] }}"
                                                        wire:model="sections.{{ $sectionIndex }}.subtopics"
                                                        name="sections[{{ $sectionIndex }}][subtopics][]"
                                                        value="{{ $subtopic['id'] }}"
                                                        type="checkbox"
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                    >
                                                </div>
                                                <div class="ml-3 leading-6">
                                                    <label class="font-medium capitalize text-gray-700">{{ $subtopic['name'] }}</label>
                                                </div>
                                            </div>

                                            <div>
                                                @can('administrate')
                                                    <span class="text-gray-500 mr-2">{{ $subcount($subtopic, $section['type']) }} Available</span>
                                                @endcan

                                                <span class="inline-flex items-center rounded-full px-1.5 py-1.5 {{ $subcount($subtopic, $section['type']) > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    <svg class="h-1.5 w-1.5 {{ $subcount($subtopic, $section['type']) > 0 ? 'fill-green-500' : 'fill-red-500' }}" viewBox="0 0 6 6" aria-hidden="true">
                                                        <circle cx="3" cy="3" r="3" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </fieldset>
        </div>
    @endforeach

    <div class="relative mt-5">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="isolate inline-flex -space-x-px">
                <button title="Remove Section" wire:click="minus()" type="button"
                        class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-l-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="feather feather-minus w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <button title="Add Section" wire:click="plus()" type="button"
                        class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-r-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="feather feather-plus w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
            </span>
        </div>
    </div>
</div>
