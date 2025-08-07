@php
    $options = [
        '' => 'Select question type',
        'multiple_choice_questions' => 'Multiple Choice',
        'true_or_false_questions' => 'True/False',
        'essay_questions' => 'Essay',
    ];

    $count = function ($topic, $type) {
        return $topic[$type . '_count'] ?? 0;
    };

    $subcount = function ($subtopic, $type) {
        return $subtopic[$type . '_count'] ?? 0;
    };

    $metafields_options=[
                    'page' => 'Insert Blank Page',
                    'external' => 'Insert Document',
                    'image' => 'Insert Image',
                    'space' => 'Insert Empty Spaces',
                ]
@endphp

<div class="mt-6 w-full max-w-full">
    <div class="flex items-center mb-6">
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">Examination Sections</h3>
            <p class="text-sm text-gray-600 mt-1">Configure your examination sections and select questions</p>
        </div>
    </div>

    @foreach ($sections as $sectionIndex => $section)
        <div
            class="mb-8 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
            <!-- Section Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-blue-700">{{ $sectionIndex + 1 }}</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Section {{ $sectionIndex + 1 }}</h4>
                            <p class="text-sm text-gray-600">Configure section details and question selection</p>
                        </div>
                    </div>
                    @if(count($sections) > 1)
                        <button wire:click="removeSection({{ $sectionIndex }})"
                                class="p-2 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors duration-200"
                                title="Remove Section">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Section Content -->
            <div class="p-6 space-y-6">
                <!-- Basic Section Info -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-1">
                        <x-form.input wire:key="name-{{ $sectionIndex }}"
                                      wire:model.live="sections.{{ $sectionIndex }}.name"
                                      name="sections[{{ $sectionIndex }}][name]"
                                      type="text"
                                      required
                                      label="Section Name"
                                      placeholder="e.g., Multiple Choice Questions"/>
                    </div>
                    <div class="lg:col-span-1">
                        <x-form.select wire:key="type-{{ $sectionIndex }}"
                                       wire:model.live="sections.{{ $sectionIndex }}.type"
                                       name="sections[{{ $sectionIndex }}][type]"
                                       label="Question Type"
                                       required
                                       value="multiple_choice_questions"
                                       :options="$options"/>
                    </div>
                    <div class="lg:col-span-1">
                        <x-form.input wire:key="count-{{ $sectionIndex }}"
                                      wire:model.live="sections.{{ $sectionIndex }}.count"
                                      name="sections[{{ $sectionIndex }}][count]"
                                      type="number"
                                      required
                                      label="Number of Questions"
                                      placeholder="0"/>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="space-y-2">
                    <div class="">
                        <x-form.rich-editor class="rich-editor"
                                            wire:key="instructions-{{ $sectionIndex }}"
                                            wire:model.live="sections.{{ $sectionIndex }}.instructions"
                                            name="sections[{{ $sectionIndex }}][instructions]"
                                            label="Section Instructions"
                                            info="Add detailed instructions for this section"
                                            type="text"/>
                    </div>
                    <p class="text-xs text-gray-500">Instructions that will appear at the beginning of this section</p>
                </div>

                <!-- Question Selection -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h5 class="text-sm font-medium text-gray-900">Question Topics</h5>
                            <p class="text-xs text-gray-600">Select topics to include questions from</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            Available questions will vary based on selected question type
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg bg-gray-50">
                        <div class="max-h-80 overflow-y-auto">
                            @foreach ($topics as $topicIndex => $topic)
                                <div class="border-b border-gray-200 last:border-b-0" x-data="{ showSubtopics: false }">
                                    <!-- Topic Row -->
                                    <div class="p-4 hover:bg-white transition-colors duration-150">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <input wire:key="topic-{{ $sectionIndex }}-{{ $topic['id'] }}"
                                                           wire:model.live="sections.{{ $sectionIndex }}.topics"
                                                           name="sections[{{ $sectionIndex }}][topics][]"
                                                           value="{{ $topic['id'] }}"
                                                           {{ $topic['questions_count'] < 1 ? 'disabled' : '' }}
                                                           type="checkbox"
                                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0"
                                                           @change="showSubtopics = $event.target.checked">
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <label class="text-sm font-medium text-gray-900 cursor-pointer">
                                                        {{ $topic['name'] }}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-3">
                                                @can('administrate')
                                                    <span
                                                        class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md">
                                                        {{ $topic['questions_count'] }} total
                                                    </span>
                                                @endcan

                                                <div class="flex items-center space-x-2">
                                                    @can('administrate')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $count($topic, $section['type']) > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        {{ $count($topic, $section['type']) }} available
                                                    </span>
                                                    @endcan
                                                    <div
                                                        class="w-2 h-2 rounded-full {{ $count($topic, $section['type']) > 0 ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subtopics -->
                                        <div x-show="showSubtopics"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                                             x-transition:enter-end="opacity-100 transform translate-y-0"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 transform translate-y-0"
                                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                                             style="display: none;"
                                             class="mt-4 pl-7 space-y-3 border-l-2 border-blue-100">
                                            @foreach ($topic['subtopics'] as $subIndex => $subtopic)
                                                <div class="bg-blue-50 rounded-lg p-3 space-y-3">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-3">
                                                            <input
                                                                wire:key="subtopic-{{ $sectionIndex }}-{{ $subtopic['id'] }}"
                                                                wire:model.live="sections.{{ $sectionIndex }}.subtopics.{{ $subIndex }}.id"
                                                                name="sections[{{ $sectionIndex }}][subtopics][{{ $subIndex }}][id]"
                                                                value="{{ $subtopic['id'] }}"
                                                                type="checkbox"
                                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                                                            <label
                                                                class="text-sm font-medium text-gray-800 capitalize cursor-pointer">
                                                                {{ $subtopic['name'] }}
                                                            </label>
                                                        </div>

                                                        <div class="flex items-center space-x-2">
                                                            @can('administrate')
                                                                <span
                                                                    class="text-xs text-gray-600 bg-white px-2 py-1 rounded">
                                                                    {{ $subcount($subtopic, $section['type']) }} available
                                                                </span>
                                                            @endcan
                                                            <div
                                                                class="w-2 h-2 rounded-full {{ $subcount($subtopic, $section['type']) > 0 ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center space-x-3">
                                                        <label
                                                            class="text-xs text-gray-600 font-medium min-w-0 flex-shrink-0">
                                                            Questions to include:
                                                        </label>
                                                        <x-form.input :has-label="false"
                                                                      class="max-w-[120px]"
                                                                      name="sections[{{ $sectionIndex }}][subtopics][{{ $subIndex }}][count]"
                                                                      wire:model.live="sections.{{ $sectionIndex }}.subtopics.{{ $subIndex }}.count"
                                                                      type="number"
                                                                      min="0"
                                                                      max="{{ $subcount($subtopic, $section['type']) }}"
                                                                      placeholder="0"/>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="border-t border-gray-200 pt-6">
                    <h5 class="text-sm font-medium text-gray-900 mb-4">Additional Options</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Blank Page Option -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <input wire:key="section-{{ $sectionIndex }}-page"
                                       wire:model.live="sections.{{ $sectionIndex }}.page"
                                       name="sections[{{ $sectionIndex }}][page]"
                                       value="blank-page"
                                       type="checkbox"
                                       class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                                <div class="min-w-0 flex-1">
                                    <label class="text-sm font-medium text-gray-900 cursor-pointer">
                                        Insert Blank Page
                                    </label>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Add a blank page after this section
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Document Upload -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="space-y-3">
                                <label class="text-sm font-medium text-gray-900">
                                    Insert Document
                                </label>
                                <p class="text-xs text-gray-600">
                                    Upload a document to include in this section
                                </p>
                                <div class="mt-2">
                                    <input wire:key="section-{{ $sectionIndex }}-document"
                                           wire:model.live="sections.{{ $sectionIndex }}.document"
                                           name="sections[{{ $sectionIndex }}][document]"
                                           type="file"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors duration-200">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add/Remove Section Controls -->
    <div class="flex items-center justify-center py-8">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-1 inline-flex">
            <button wire:click="minus()"
                    type="button"
                    title="Remove Section"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-transparent rounded-l-md hover:bg-gray-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                {{ count($sections) <= 1 ? 'disabled' : '' }}>
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                </svg>
                Remove Section
            </button>

            <div class="w-px bg-gray-200"></div>

            <button wire:click="plus()"
                    type="button"
                    title="Add Section"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Add Section
            </button>
        </div>
    </div>
</div>
