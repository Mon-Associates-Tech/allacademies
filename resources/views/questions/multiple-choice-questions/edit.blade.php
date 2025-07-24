<x-layouts.app title="Edit Multiple Choice Question" :has-action="false">
        <x-slot name="breadcrumb">
            <x-breadcrumb :paths="[
                'Academic Groups' => route('academic-groups.index'),
                $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                'Academic Levels' => route('academic-levels.index', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                $multipleChoiceQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                'Academic Topics' => route('academic-topics.index', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                $multipleChoiceQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                'Multiple Choice Questions' => route('multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                'Edit' => null,
            ]" />
        </x-slot>

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Multiple Choice Question</h1>
                        <p class="text-gray-600">Topic: {{ $multipleChoiceQuestion->academicTopic->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <form method="POST" action="{{ route('multiple-choice-questions.update', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Question Details Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Difficulty Level -->
                                <div>
                                    <x-form.select
                                        name="difficulty_level"
                                        label="Difficulty Level"
                                        :options="[
                                            'unspecified' => 'Unspecified',
                                            'easy' => 'Easy',
                                            'medium' => 'Medium',
                                            'difficult' => 'Difficult',
                                        ]"
                                        :value="$multipleChoiceQuestion->difficulty_level"
                                    />
                                </div>

                                <!-- Score -->
                                <div>
                                    <x-form.input
                                        name="score"
                                        type="number"
                                        label="Question Score"
                                        :value="$multipleChoiceQuestion->score"
                                        min="0"
                                        step="1"
                                    />
                                </div>

                                <!-- Sub Topic -->
                                <div class="md:col-span-2" x-data="{ showCustomInput: false, selectedValue: '{{ $multipleChoiceQuestion?->subtopic?->name ?? '' }}' }">
                                    <label for="subtopic_select" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sub Topic
                                    </label>

                                    @if($multipleChoiceQuestion->academicTopic->subtopics->count() > 0)
                                        <select
                                            id="subtopic_select"
                                            x-model="selectedValue"
                                            @change="showCustomInput = (selectedValue === 'new')"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Enter subtopic or leave blank</option>
                                            @foreach($multipleChoiceQuestion->academicTopic->subtopics as $subtopic)
                                                <option value="{{ $subtopic->name }}"
                                                    {{ (old('subtopic', $multipleChoiceQuestion?->subtopic?->name) == $subtopic->name) ? 'selected' : '' }}>
                                                    {{ $subtopic->name }}
                                                </option>
                                            @endforeach
                                            <option value="new">+ Create New Subtopic</option>
                                        </select>

                                        <!-- Hidden input for existing subtopic selection -->
                                        <input
                                            type="hidden"
                                            name="subtopic"
                                            :value="selectedValue !== 'new' ? selectedValue : ''"
                                            x-show="!showCustomInput"
                                        />

                                        <!-- Custom input for new subtopic -->
                                        <div x-show="showCustomInput" x-transition class="mt-2">
                                            <x-form.input
                                                type="text"
                                                name="subtopic"
                                                label="New Subtopic Name"
                                                placeholder="Enter new subtopic name"
                                            />
                                        </div>
                                    @else
                                        {{-- Fallback: Simple text input if no subtopics exist --}}
                                        <x-form.input
                                            type="text"
                                            label="Sub Topic"
                                            :value="$multipleChoiceQuestion?->subtopic?->name"
                                            name="subtopic"
                                            placeholder="Enter subtopic name"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">No existing subtopics found. Enter a new one above.</p>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Question Content Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Question Content</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Question Text -->
                                <div>
{{--                                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>--}}
                                    <x-form.rich-editor full name="question" :value="$multipleChoiceQuestion->question" />
                                </div>

                                <!-- Answer Options -->
                                <div class="space-y-4">
                                    <label class="block text-sm font-medium text-gray-700">Answer Options</label>
                                    @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                        <div class="relative">
                                            <div class="absolute left-0 top-0 flex items-center justify-center w-8 h-8 rounded-full {{ $multipleChoiceQuestion->answer === $option ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                                                {{ strtoupper($option) }}
                                            </div>
                                            <div class="ml-12">
                                                <x-form.rich-editor
                                                    full
                                                    name="option_{{ $option }}"
                                                    :value="$multipleChoiceQuestion->{'option_'.$option}"
                                                />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Correct Answer -->
                                <div>
                                    <x-form.select
                                        name="answer"
                                        label="Correct Answer"
                                        :options="[
                                            'a' => 'Option A',
                                            'b' => 'Option B',
                                            'c' => 'Option C',
                                            'd' => 'Option D',
                                            'e' => 'Option E',
                                        ]"
                                        :value="$multipleChoiceQuestion->answer"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6">
                        <x-link.secondary :to="route('multiple-choice-questions.index', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </x-link.secondary>

                        <x-button.primary type="submit">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Question
                        </x-button.primary>
                    </div>
                </div>
            </form>
        </div>

        <x-slot name="right">
            <div class="mt-5">
                <x-plugins />
            </div>
        </x-slot>
    </x-layouts.app>
